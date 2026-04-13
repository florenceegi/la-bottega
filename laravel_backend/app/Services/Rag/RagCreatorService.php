<?php

declare(strict_types=1);

/**
 * @package App\Services\Rag
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Hybrid retrieval service per RAG Creator — vector search (pgvector HNSW)
 *          + Full-Text Search italiano, con reranking Voyage AI (primario) / Cohere (fallback).
 *          Filtra per categoria, career_level, percorso, medium.
 */

namespace App\Services\Rag;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

class RagCreatorService
{
    public function __construct(
        private readonly UltraLogManager $logger,
        private readonly ErrorManagerInterface $errorManager,
    ) {}

    /**
     * Hybrid search: vector similarity + FTS con reranking.
     *
     * @param string $query Testo della query utente
     * @param array  $filters Filtri opzionali: category, career_level, percorso, medium
     * @return array Array di chunk con score finale
     */
    public function search(string $query, array $filters = []): array
    {
        $config = config('rag_creator.retrieval');

        $embedding = $this->generateEmbedding($query);
        if ($embedding === null) {
            return [];
        }

        $vectorResults = $this->vectorSearch($embedding, $filters, $config['top_k']);
        $ftsResults = $this->fullTextSearch($query, $filters, $config['top_k']);

        $merged = $this->mergeResults(
            $vectorResults,
            $ftsResults,
            $config['vector_weight'],
            $config['fts_weight']
        );

        if (empty($merged)) {
            return [];
        }

        $reranked = $this->rerank($query, $merged, $config['rerank_top_n']);

        return $reranked;
    }

    /**
     * Genera embedding via OpenAI text-embedding-3-small.
     *
     * @return float[]|null Vettore 1536-dim o null se errore
     */
    public function generateEmbedding(string $text): ?array
    {
        $model = config('rag_creator.embedding_model', 'text-embedding-3-small');

        try {
            $response = Http::withToken(config('services.openai.api_key'))
                ->timeout(config('services.openai.timeout', 30))
                ->post(config('services.openai.base_url') . '/embeddings', [
                    'model' => $model,
                    'input' => $text,
                ]);

            if ($response->failed()) {
                $this->logger->warning('OpenAI embedding request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            return $response->json('data.0.embedding');
        } catch (\Exception $e) {
            return $this->errorManager->handle('BOTTEGA_EMBEDDING_ERROR', [
                'model' => $model,
                'text_length' => strlen($text),
            ], $e);
        }
    }

    /**
     * Vector similarity search via pgvector cosine distance.
     */
    private function vectorSearch(array $embedding, array $filters, int $topK): array
    {
        $embeddingStr = '[' . implode(',', $embedding) . ']';
        $threshold = config('rag_creator.retrieval.similarity_threshold', 0.45);

        $where = ['1 - (embedding <=> :embedding) >= :threshold'];
        $bindings = [
            'embedding' => $embeddingStr,
            'threshold' => $threshold,
        ];

        $this->applyFilters($where, $bindings, $filters);

        $sql = sprintf(
            "SELECT id, parent_document_id, document_id, chunk_index, text,
                    doc_title, doc_category, doc_subcategory,
                    doc_target_medium, doc_target_career_level, doc_target_percorso,
                    1 - (embedding <=> :embedding_select) AS similarity
             FROM bottega.rag_creator_chunks
             WHERE %s
             ORDER BY similarity DESC
             LIMIT :top_k",
            implode(' AND ', $where)
        );
        $bindings['embedding_select'] = $embeddingStr;
        $bindings['top_k'] = $topK;

        try {
            $results = DB::select($sql, $bindings);
            return array_map(fn ($row) => (array) $row, $results);
        } catch (\Exception $e) {
            $this->errorManager->handle('BOTTEGA_VECTOR_SEARCH_ERROR', [
                'filters' => $filters,
            ], $e);
            return [];
        }
    }

    /**
     * Full-text search italiano su search_vector (tsvector).
     */
    private function fullTextSearch(string $query, array $filters, int $topK): array
    {
        $where = ["search_vector @@ plainto_tsquery('italian', :query)"];
        $bindings = ['query' => $query];

        $this->applyFilters($where, $bindings, $filters);

        $sql = sprintf(
            "SELECT id, parent_document_id, document_id, chunk_index, text,
                    doc_title, doc_category, doc_subcategory,
                    doc_target_medium, doc_target_career_level, doc_target_percorso,
                    ts_rank_cd(search_vector, plainto_tsquery('italian', :query_rank)) AS fts_rank
             FROM bottega.rag_creator_chunks
             WHERE %s
             ORDER BY fts_rank DESC
             LIMIT :top_k",
            implode(' AND ', $where)
        );
        $bindings['query_rank'] = $query;
        $bindings['top_k'] = $topK;

        try {
            $results = DB::select($sql, $bindings);
            return array_map(fn ($row) => (array) $row, $results);
        } catch (\Exception $e) {
            $this->errorManager->handle('BOTTEGA_FTS_SEARCH_ERROR', [
                'query' => $query,
                'filters' => $filters,
            ], $e);
            return [];
        }
    }

    /**
     * Applica filtri opzionali alla query SQL.
     */
    private function applyFilters(array &$where, array &$bindings, array $filters): void
    {
        if (!empty($filters['category'])) {
            $where[] = 'doc_category = :category';
            $bindings['category'] = $filters['category'];
        }
        if (!empty($filters['career_level']) && $filters['career_level'] !== 'all') {
            $where[] = "(doc_target_career_level = :career_level OR doc_target_career_level = 'all')";
            $bindings['career_level'] = $filters['career_level'];
        }
        if (!empty($filters['percorso']) && $filters['percorso'] !== 'all') {
            $where[] = "(doc_target_percorso = :percorso OR doc_target_percorso = 'all')";
            $bindings['percorso'] = $filters['percorso'];
        }
        if (!empty($filters['medium'])) {
            $where[] = 'doc_target_medium = :medium';
            $bindings['medium'] = $filters['medium'];
        }
    }

    /**
     * Merge risultati vector + FTS con pesi configurabili.
     * Deduplica per chunk id, somma score pesati.
     */
    private function mergeResults(
        array $vectorResults,
        array $ftsResults,
        float $vectorWeight,
        float $ftsWeight
    ): array {
        $merged = [];

        foreach ($vectorResults as $row) {
            $merged[$row['id']] = $row;
            $merged[$row['id']]['score'] = ($row['similarity'] ?? 0) * $vectorWeight;
        }

        foreach ($ftsResults as $row) {
            if (isset($merged[$row['id']])) {
                $merged[$row['id']]['score'] += ($row['fts_rank'] ?? 0) * $ftsWeight;
            } else {
                $merged[$row['id']] = $row;
                $merged[$row['id']]['score'] = ($row['fts_rank'] ?? 0) * $ftsWeight;
            }
        }

        usort($merged, fn ($a, $b) => $b['score'] <=> $a['score']);

        return array_values($merged);
    }

    /**
     * Rerank tramite Voyage AI (primario) con fallback Cohere.
     *
     * @param string $query Query originale
     * @param array  $chunks Chunk da rerankare
     * @param int    $topN Numero massimo di risultati finali
     * @return array Chunk rerankati
     */
    private function rerank(string $query, array $chunks, int $topN): array
    {
        $rerankerConfig = config('rag_creator.reranker');

        $reranked = $this->rerankVoyage($query, $chunks, $topN, $rerankerConfig['voyage_model']);

        if ($reranked === null) {
            $this->logger->warning('Voyage rerank failed, falling back to Cohere');
            $reranked = $this->rerankCohere($query, $chunks, $topN, $rerankerConfig['cohere_model']);
        }

        if ($reranked === null) {
            $this->logger->warning('Both rerankers failed, returning merged results');
            return array_slice($chunks, 0, $topN);
        }

        return $reranked;
    }

    /**
     * Rerank via Voyage AI rerank-2.
     */
    private function rerankVoyage(string $query, array $chunks, int $topN, string $model): ?array
    {
        try {
            $response = Http::withToken(config('services.voyage.api_key'))
                ->timeout(config('services.voyage.timeout', 15))
                ->post(config('services.voyage.base_url') . '/rerank', [
                    'model' => $model,
                    'query' => $query,
                    'documents' => array_map(fn ($c) => $c['text'], $chunks),
                    'top_k' => $topN,
                ]);

            if ($response->failed()) {
                $this->logger->warning('Voyage rerank HTTP error', [
                    'status' => $response->status(),
                ]);
                return null;
            }

            return $this->applyRerankScores($chunks, $response->json('data'), $topN);
        } catch (\Exception $e) {
            $this->errorManager->handle('BOTTEGA_VOYAGE_RERANK_ERROR', [], $e);
            return null;
        }
    }

    /**
     * Rerank via Cohere rerank-multilingual-v3.0 (fallback).
     */
    private function rerankCohere(string $query, array $chunks, int $topN, string $model): ?array
    {
        try {
            $response = Http::withToken(config('services.cohere.api_key'))
                ->timeout(config('services.cohere.timeout', 15))
                ->post(config('services.cohere.base_url') . '/rerank', [
                    'model' => $model,
                    'query' => $query,
                    'documents' => array_map(fn ($c) => ['text' => $c['text']], $chunks),
                    'top_n' => $topN,
                ]);

            if ($response->failed()) {
                $this->logger->warning('Cohere rerank HTTP error', [
                    'status' => $response->status(),
                ]);
                return null;
            }

            return $this->applyRerankScores($chunks, $response->json('results'), $topN);
        } catch (\Exception $e) {
            $this->errorManager->handle('BOTTEGA_COHERE_RERANK_ERROR', [], $e);
            return null;
        }
    }

    /**
     * Applica i punteggi del reranker ai chunk originali.
     */
    private function applyRerankScores(array $chunks, ?array $rerankResults, int $topN): ?array
    {
        if (empty($rerankResults)) {
            return null;
        }

        $reranked = [];
        foreach (array_slice($rerankResults, 0, $topN) as $result) {
            $index = $result['index'] ?? null;
            $score = $result['relevance_score'] ?? ($result['score'] ?? 0);

            if ($index !== null && isset($chunks[$index])) {
                $chunk = $chunks[$index];
                $chunk['rerank_score'] = $score;
                $reranked[] = $chunk;
            }
        }

        return !empty($reranked) ? $reranked : null;
    }
}
