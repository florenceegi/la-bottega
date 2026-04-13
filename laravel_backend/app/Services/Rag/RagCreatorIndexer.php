<?php

declare(strict_types=1);

/**
 * @package App\Services\Rag
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Pipeline di indexing per RAG Creator — chunking testo, generazione embeddings
 *          via OpenAI, inserimento in bottega.rag_creator_documents + rag_creator_chunks.
 *          Supporta upsert (document_id univoco), batch embedding (max 20 per request).
 */

namespace App\Services\Rag;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

class RagCreatorIndexer
{
    private const EMBEDDING_BATCH_SIZE = 20;
    private const EMBEDDING_BATCH_DELAY_MS = 100;

    public function __construct(
        private readonly RagCreatorService $ragService,
        private readonly UltraLogManager $logger,
        private readonly ErrorManagerInterface $errorManager,
    ) {}

    /**
     * Indicizza un documento completo: crea record documento, chunka il testo,
     * genera embeddings e inserisce i chunk.
     *
     * @param array $document Dati documento: title, raw_text, category, + opzionali
     * @return array{document_id: string, chunks_count: int}|null
     */
    public function indexDocument(array $document): ?array
    {
        $documentId = $document['document_id'] ?? $this->generateDocumentId($document['title']);

        DB::beginTransaction();
        try {
            $parentId = $this->upsertDocument($documentId, $document);

            $chunks = $this->chunkText($document['raw_text']);

            $this->deleteExistingChunks($parentId);

            $embeddings = $this->batchGenerateEmbeddings(
                array_map(fn ($c) => $c['text'], $chunks)
            );

            if (empty($embeddings)) {
                DB::rollBack();
                $this->logger->warning('Failed to generate embeddings, aborting indexing', [
                    'document_id' => $documentId,
                ]);
                return null;
            }

            $docEmbedding = $this->ragService->generateEmbedding($document['title'] . ' ' . $document['category']);
            if ($docEmbedding !== null) {
                $embStr = '[' . implode(',', $docEmbedding) . ']';
                DB::update(
                    'UPDATE bottega.rag_creator_documents SET embedding = :emb, indexed_at = NOW() WHERE id = :id',
                    ['emb' => $embStr, 'id' => $parentId]
                );
            }

            $this->insertChunks($parentId, $documentId, $document, $chunks, $embeddings);

            DB::commit();

            $this->logger->info('Document indexed successfully', [
                'document_id' => $documentId,
                'chunks_count' => count($chunks),
            ]);

            return [
                'document_id' => $documentId,
                'chunks_count' => count($chunks),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorManager->handle('BOTTEGA_INDEX_ERROR', [
                'document_id' => $documentId,
            ], $e);
            return null;
        }
    }

    /**
     * Indicizza un batch di documenti.
     *
     * @param array[] $documents
     * @return array{indexed: int, failed: int, results: array}
     */
    public function indexBatch(array $documents): array
    {
        $indexed = 0;
        $failed = 0;
        $results = [];

        foreach ($documents as $doc) {
            $result = $this->indexDocument($doc);
            if ($result !== null) {
                $indexed++;
                $results[] = $result;
            } else {
                $failed++;
            }
        }

        $this->logger->info('Batch indexing complete', [
            'indexed' => $indexed,
            'failed' => $failed,
        ]);

        return compact('indexed', 'failed', 'results');
    }

    /**
     * Upsert record documento in rag_creator_documents.
     */
    private function upsertDocument(string $documentId, array $data): int
    {
        $existing = DB::selectOne(
            'SELECT id FROM bottega.rag_creator_documents WHERE document_id = :did',
            ['did' => $documentId]
        );

        if ($existing) {
            DB::update(
                "UPDATE bottega.rag_creator_documents SET
                    title = :title, raw_text = :raw_text, source_url = :source_url,
                    source_type = :source_type, category = :category, subcategory = :subcategory,
                    tags = :tags, language = :language, target_medium = :medium,
                    target_career_level = :level, target_percorso = :percorso,
                    geographic_scope = :geo, metadata_extra = :meta, status = 'active'
                 WHERE document_id = :did",
                [
                    'title' => $data['title'],
                    'raw_text' => $data['raw_text'] ?? null,
                    'source_url' => $data['source_url'] ?? null,
                    'source_type' => $data['source_type'] ?? 'manual',
                    'category' => $data['category'],
                    'subcategory' => $data['subcategory'] ?? null,
                    'tags' => json_encode($data['tags'] ?? []),
                    'language' => $data['language'] ?? 'it',
                    'medium' => $data['target_medium'] ?? null,
                    'level' => $data['target_career_level'] ?? null,
                    'percorso' => $data['target_percorso'] ?? null,
                    'geo' => $data['geographic_scope'] ?? null,
                    'meta' => json_encode($data['metadata_extra'] ?? []),
                    'did' => $documentId,
                ]
            );
            return $existing->id;
        }

        DB::insert(
            "INSERT INTO bottega.rag_creator_documents
                (document_id, title, raw_text, source_url, source_type,
                 category, subcategory, tags, language,
                 target_medium, target_career_level, target_percorso,
                 geographic_scope, metadata_extra)
             VALUES
                (:did, :title, :raw_text, :source_url, :source_type,
                 :category, :subcategory, :tags, :language,
                 :medium, :level, :percorso,
                 :geo, :meta)",
            [
                'did' => $documentId,
                'title' => $data['title'],
                'raw_text' => $data['raw_text'] ?? null,
                'source_url' => $data['source_url'] ?? null,
                'source_type' => $data['source_type'] ?? 'manual',
                'category' => $data['category'],
                'subcategory' => $data['subcategory'] ?? null,
                'tags' => json_encode($data['tags'] ?? []),
                'language' => $data['language'] ?? 'it',
                'medium' => $data['target_medium'] ?? null,
                'level' => $data['target_career_level'] ?? null,
                'percorso' => $data['target_percorso'] ?? null,
                'geo' => $data['geographic_scope'] ?? null,
                'meta' => json_encode($data['metadata_extra'] ?? []),
            ]
        );

        return DB::selectOne(
            'SELECT id FROM bottega.rag_creator_documents WHERE document_id = :did',
            ['did' => $documentId]
        )->id;
    }

    /**
     * Elimina chunk esistenti (per re-indexing).
     */
    private function deleteExistingChunks(int $parentId): void
    {
        DB::delete(
            'DELETE FROM bottega.rag_creator_chunks WHERE parent_document_id = :pid',
            ['pid' => $parentId]
        );
    }

    /**
     * Chunking testo con overlap configurabile.
     *
     * @return array{text: string, chunk_index: int, char_count: int}[]
     */
    private function chunkText(string $text): array
    {
        $maxSize = config('rag_creator.chunking.max_chunk_size', 800);
        $overlap = config('rag_creator.chunking.overlap', 100);

        $paragraphs = preg_split('/\n{2,}/', trim($text));
        $chunks = [];
        $currentChunk = '';
        $chunkIndex = 0;

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if ($paragraph === '') {
                continue;
            }

            if (strlen($currentChunk) + strlen($paragraph) + 2 <= $maxSize) {
                $currentChunk .= ($currentChunk !== '' ? "\n\n" : '') . $paragraph;
            } else {
                if ($currentChunk !== '') {
                    $chunks[] = [
                        'text' => $currentChunk,
                        'chunk_index' => $chunkIndex++,
                        'char_count' => strlen($currentChunk),
                    ];
                }

                if (strlen($paragraph) > $maxSize) {
                    $sentences = preg_split('/(?<=[.!?])\s+/', $paragraph);
                    $currentChunk = '';
                    foreach ($sentences as $sentence) {
                        if (strlen($currentChunk) + strlen($sentence) + 1 <= $maxSize) {
                            $currentChunk .= ($currentChunk !== '' ? ' ' : '') . $sentence;
                        } else {
                            if ($currentChunk !== '') {
                                $chunks[] = [
                                    'text' => $currentChunk,
                                    'chunk_index' => $chunkIndex++,
                                    'char_count' => strlen($currentChunk),
                                ];
                            }
                            $currentChunk = $sentence;
                        }
                    }
                } else {
                    $overlapText = $this->getOverlapSuffix($currentChunk ?? '', $overlap);
                    $currentChunk = $overlapText !== '' ? $overlapText . "\n\n" . $paragraph : $paragraph;
                }
            }
        }

        if ($currentChunk !== '') {
            $chunks[] = [
                'text' => $currentChunk,
                'chunk_index' => $chunkIndex,
                'char_count' => strlen($currentChunk),
            ];
        }

        return $chunks;
    }

    /**
     * Estrae suffisso per overlap tra chunk consecutivi.
     */
    private function getOverlapSuffix(string $text, int $overlapChars): string
    {
        if (strlen($text) <= $overlapChars) {
            return $text;
        }

        $suffix = substr($text, -$overlapChars);
        $spacePos = strpos($suffix, ' ');

        return $spacePos !== false ? substr($suffix, $spacePos + 1) : $suffix;
    }

    /**
     * Genera embeddings in batch (max 20 per request OpenAI).
     *
     * @param string[] $texts
     * @return float[][]|null Array di vettori embedding
     */
    private function batchGenerateEmbeddings(array $texts): ?array
    {
        $model = config('rag_creator.embedding_model', 'text-embedding-3-small');
        $batches = array_chunk($texts, self::EMBEDDING_BATCH_SIZE);
        $allEmbeddings = [];

        foreach ($batches as $batchIndex => $batch) {
            if ($batchIndex > 0) {
                usleep(self::EMBEDDING_BATCH_DELAY_MS * 1000);
            }

            try {
                $response = \Illuminate\Support\Facades\Http::withToken(config('services.openai.api_key'))
                    ->timeout(config('services.openai.timeout', 30))
                    ->post(config('services.openai.base_url') . '/embeddings', [
                        'model' => $model,
                        'input' => $batch,
                    ]);

                if ($response->failed()) {
                    $this->logger->warning('Batch embedding request failed', [
                        'batch_index' => $batchIndex,
                        'status' => $response->status(),
                    ]);
                    return null;
                }

                $data = $response->json('data');
                foreach ($data as $item) {
                    $allEmbeddings[] = $item['embedding'];
                }
            } catch (\Exception $e) {
                $this->errorManager->handle('BOTTEGA_BATCH_EMBEDDING_ERROR', [
                    'batch_index' => $batchIndex,
                    'batch_size' => count($batch),
                ], $e);
                return null;
            }
        }

        return count($allEmbeddings) === count($texts) ? $allEmbeddings : null;
    }

    /**
     * Inserisce chunk con embedding e metadati denormalizzati.
     */
    private function insertChunks(
        int $parentId,
        string $documentId,
        array $document,
        array $chunks,
        array $embeddings
    ): void {
        foreach ($chunks as $i => $chunk) {
            $embStr = '[' . implode(',', $embeddings[$i]) . ']';
            $textHash = hash('sha256', $chunk['text']);

            DB::insert(
                "INSERT INTO bottega.rag_creator_chunks
                    (parent_document_id, document_id, chunk_index, text, char_count,
                     embedding, text_hash,
                     doc_title, doc_category, doc_subcategory,
                     doc_target_medium, doc_target_career_level, doc_target_percorso,
                     doc_metadata)
                 VALUES
                    (:pid, :did, :ci, :text, :cc,
                     :emb, :hash,
                     :title, :cat, :subcat,
                     :medium, :level, :percorso,
                     :meta)",
                [
                    'pid' => $parentId,
                    'did' => $documentId,
                    'ci' => $chunk['chunk_index'],
                    'text' => $chunk['text'],
                    'cc' => $chunk['char_count'],
                    'emb' => $embStr,
                    'hash' => $textHash,
                    'title' => $document['title'],
                    'cat' => $document['category'],
                    'subcat' => $document['subcategory'] ?? null,
                    'medium' => $document['target_medium'] ?? null,
                    'level' => $document['target_career_level'] ?? null,
                    'percorso' => $document['target_percorso'] ?? null,
                    'meta' => json_encode($document['metadata_extra'] ?? []),
                ]
            );
        }
    }

    /**
     * Genera document_id deterministico dal titolo.
     */
    private function generateDocumentId(string $title): string
    {
        return 'doc_' . Str::slug($title) . '_' . substr(hash('sha256', $title), 0, 8);
    }
}
