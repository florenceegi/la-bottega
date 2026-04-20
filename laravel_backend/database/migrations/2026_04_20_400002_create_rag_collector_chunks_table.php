<?php

declare(strict_types=1);

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-20
 * @purpose Tabella RAG Collector chunks — frammenti con embedding inline per vector search.
 *          Metadati documento denormalizzati per query senza JOIN.
 *          HNSW index con ef_construction=64 (volume iniziale contenuto).
 *          Schema: bottega.rag_collector_chunks
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE bottega.rag_collector_chunks (
                id                          BIGSERIAL PRIMARY KEY,
                parent_document_id          BIGINT NOT NULL REFERENCES bottega.rag_collector_documents(id) ON DELETE CASCADE,
                document_id                 VARCHAR(255) NOT NULL,

                -- Dati chunk
                chunk_index                 INTEGER NOT NULL,
                text                        TEXT NOT NULL,
                char_count                  INTEGER,
                section_type                VARCHAR(100),

                -- Embedding inline (1536 dim OpenAI text-embedding-3-small)
                embedding                   vector(1536) NOT NULL,
                embedding_model             VARCHAR(100) DEFAULT 'openai.text-embedding-3-small',
                text_hash                   VARCHAR(64),

                -- Metadata documento denormalizzata (vector search senza JOIN)
                doc_title                   TEXT,
                doc_category                VARCHAR(100),
                doc_subcategory             VARCHAR(100),
                doc_target_expertise_level  VARCHAR(50),
                doc_target_collector_type   VARCHAR(50),
                doc_target_medium_interest  VARCHAR(100),
                doc_price_bracket           VARCHAR(50),
                doc_metadata                JSONB DEFAULT '{}',

                -- Full-text search
                search_vector               tsvector,

                created_at                  TIMESTAMP DEFAULT NOW(),
                updated_at                  TIMESTAMP DEFAULT NOW(),

                CONSTRAINT uq_rag_collector_chunk_parent_idx UNIQUE (parent_document_id, chunk_index)
            )
        ");

        // B-tree indexes
        DB::statement('CREATE INDEX idx_rag_collector_chunk_parent ON bottega.rag_collector_chunks(parent_document_id)');
        DB::statement('CREATE INDEX idx_rag_collector_chunk_docid ON bottega.rag_collector_chunks(document_id)');
        DB::statement('CREATE INDEX idx_rag_collector_chunk_category ON bottega.rag_collector_chunks(doc_category)');
        DB::statement('CREATE INDEX idx_rag_collector_chunk_expertise ON bottega.rag_collector_chunks(doc_target_expertise_level)');
        DB::statement('CREATE INDEX idx_rag_collector_chunk_type ON bottega.rag_collector_chunks(doc_target_collector_type)');
        DB::statement('CREATE INDEX idx_rag_collector_chunk_medium ON bottega.rag_collector_chunks(doc_target_medium_interest)');
        DB::statement('CREATE INDEX idx_rag_collector_chunk_price ON bottega.rag_collector_chunks(doc_price_bracket)');
        DB::statement('CREATE INDEX idx_rag_collector_chunk_hash ON bottega.rag_collector_chunks(text_hash)');

        // Full-text search GIN index
        DB::statement('CREATE INDEX idx_rag_collector_chunk_fts ON bottega.rag_collector_chunks USING gin(search_vector)');

        // Vector HNSW index (cosine distance)
        DB::statement('CREATE INDEX idx_rag_collector_chunk_embedding ON bottega.rag_collector_chunks USING hnsw (embedding vector_cosine_ops) WITH (m = 16, ef_construction = 64)');

        // Trigger: auto-update tsvector (doc_title=A, chunk text=B)
        DB::statement("
            CREATE OR REPLACE FUNCTION bottega.rag_collector_chunks_fts_trigger() RETURNS trigger AS \$\$
            BEGIN
                NEW.search_vector :=
                    setweight(to_tsvector('italian', coalesce(NEW.doc_title, '')), 'A') ||
                    setweight(to_tsvector('italian', coalesce(NEW.text, '')), 'B');
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql
        ");

        DB::statement("
            CREATE TRIGGER rag_collector_chunks_fts_update
                BEFORE INSERT OR UPDATE OF text, doc_title
                ON bottega.rag_collector_chunks
                FOR EACH ROW
                EXECUTE FUNCTION bottega.rag_collector_chunks_fts_trigger()
        ");

        // Trigger: auto-update updated_at
        DB::statement("
            CREATE OR REPLACE FUNCTION bottega.rag_collector_chunks_updated_at() RETURNS trigger AS \$\$
            BEGIN
                NEW.updated_at = NOW();
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql
        ");

        DB::statement("
            CREATE TRIGGER rag_collector_chunks_updated_at
                BEFORE UPDATE ON bottega.rag_collector_chunks
                FOR EACH ROW
                EXECUTE FUNCTION bottega.rag_collector_chunks_updated_at()
        ");
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS rag_collector_chunks_updated_at ON bottega.rag_collector_chunks');
        DB::statement('DROP TRIGGER IF EXISTS rag_collector_chunks_fts_update ON bottega.rag_collector_chunks');
        DB::statement('DROP FUNCTION IF EXISTS bottega.rag_collector_chunks_updated_at()');
        DB::statement('DROP FUNCTION IF EXISTS bottega.rag_collector_chunks_fts_trigger()');
        DB::statement('DROP TABLE IF EXISTS bottega.rag_collector_chunks CASCADE');
    }
};
