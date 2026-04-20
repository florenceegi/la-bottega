<?php

/**
 * @package Config
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-20
 * @purpose Configurazione RAG Collector — categorie, embedding model, retrieval params.
 *          Corpus educativo per il collezionista: glossario mercato, etica, storia artisti EGI.
 *          Voce identitaria: consiglio oggettivo, MAI promozionale.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Embedding Model
    |--------------------------------------------------------------------------
    */
    'embedding_model' => env('RAG_COLLECTOR_EMBEDDING_MODEL', 'text-embedding-3-small'),
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
    | Document Categories (D.1.1)
    |--------------------------------------------------------------------------
    | Tassonomia approvata Fabio 2026-04-20. MAI categorie promozionali.
    */
    'categories' => [
        'glossario_mercato' => [
            'label' => 'Glossario mercato arte',
            'description' => 'Edizione, provenance, attribuzione, condition report, autenticazione',
        ],
        'etica_collezionismo' => [
            'label' => 'Etica del collezionismo',
            'description' => 'Provenance, restituzioni, sostenibilità, diritti artisti, resale-right',
        ],
        'storia_artisti_egi' => [
            'label' => 'Storia artisti FlorenceEGI',
            'description' => 'Biografie, percorso, stile, opere chiave dei creator della piattaforma',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Expertise Level (per filtraggio contestuale)
    |--------------------------------------------------------------------------
    */
    'expertise_levels' => ['beginner', 'intermediate', 'advanced', 'all'],

    /*
    |--------------------------------------------------------------------------
    | Collector Type (per filtraggio contestuale)
    |--------------------------------------------------------------------------
    */
    'collector_types' => ['emerging', 'established', 'institutional', 'all'],

    /*
    |--------------------------------------------------------------------------
    | Medium Interest (per filtraggio contestuale)
    |--------------------------------------------------------------------------
    */
    'medium_interests' => ['pittura', 'scultura', 'digitale', 'fotografia', 'installazione', 'all'],

    /*
    |--------------------------------------------------------------------------
    | Price Bracket (per filtraggio contestuale)
    |--------------------------------------------------------------------------
    */
    'price_brackets' => ['entry', 'mid', 'high', 'institutional', 'all'],
];
