<?php

/**
 * @package Config
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Configurazione RAG Creator — categorie documenti, embedding model, retrieval params
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Embedding Model
    |--------------------------------------------------------------------------
    */
    'embedding_model' => env('RAG_CREATOR_EMBEDDING_MODEL', 'text-embedding-3-small'),
    'embedding_dimensions' => 1536,

    /*
    |--------------------------------------------------------------------------
    | Retrieval Parameters
    |--------------------------------------------------------------------------
    */
    'retrieval' => [
        'top_k' => 10,
        'similarity_threshold' => 0.45,
        'rerank_top_n' => 5,
        'fts_weight' => 0.3,
        'vector_weight' => 0.7,
    ],

    /*
    |--------------------------------------------------------------------------
    | Reranker
    |--------------------------------------------------------------------------
    */
    'reranker' => [
        'primary' => 'voyage',
        'fallback' => 'cohere',
        'voyage_model' => env('VOYAGE_RERANK_MODEL', 'rerank-2'),
        'cohere_model' => env('COHERE_RERANK_MODEL', 'rerank-multilingual-v3.0'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Chunking
    |--------------------------------------------------------------------------
    */
    'chunking' => [
        'max_chunk_size' => 800,
        'overlap' => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | Document Categories (A.5.2)
    |--------------------------------------------------------------------------
    | Ogni documento RAG Creator appartiene a una di queste categorie.
    | Usate per filtri nella retrieval e per contestualizzare i risultati.
    */
    'categories' => [
        'case_study' => [
            'label' => 'Caso studio artista',
            'description' => 'Strategie documentate di artisti emergenti e affermati',
        ],
        'marketing_guide' => [
            'label' => 'Guida marketing artistico',
            'description' => 'Best practice per promozione, brand building, social media',
        ],
        'pricing_logic' => [
            'label' => 'Logica di pricing',
            'description' => 'Strategie prezzi per medium, dimensione, edizioni, geografia',
        ],
        'bio_writing' => [
            'label' => 'Scrittura bio e statement',
            'description' => 'Guida per bio, artist statement, descrizioni opere efficaci',
        ],
        'market_analysis' => [
            'label' => 'Analisi di mercato',
            'description' => 'Trend, dati vendite, benchmark per medium e segmento',
        ],
        'opportunity' => [
            'label' => 'Opportunità',
            'description' => 'Call for artists, fiere, residenze, premi, bandi',
        ],
        'narrative_strategy' => [
            'label' => 'Strategia narrativa',
            'description' => 'Costruzione narrative vincenti per presentazioni e portfolio',
        ],
        'digital_presence' => [
            'label' => 'Presenza digitale',
            'description' => 'Ottimizzazione profili online, sito artista, newsletter, SEO',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Career Levels (per filtraggio contestuale)
    |--------------------------------------------------------------------------
    */
    'career_levels' => ['emerging', 'mid_career', 'established', 'all'],

    /*
    |--------------------------------------------------------------------------
    | Target Percorsi (per filtraggio contestuale)
    |--------------------------------------------------------------------------
    */
    'target_percorsi' => ['zero', 'crescita', 'mercato', 'all'],
];
