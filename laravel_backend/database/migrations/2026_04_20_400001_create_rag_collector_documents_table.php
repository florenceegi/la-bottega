<?php

declare(strict_types=1);

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-20
 * @purpose Tabella RAG Collector — documenti sorgente per il Maestro Collector.
 *          Corpus educativo: glossario mercato, etica collezionismo, storia artisti FlorenceEGI.
 *          Embeddings vector(1536) + FTS italiano + HNSW. MAI contenuti promozionali.
 *          Schema: bottega.rag_collector_documents
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector');

        DB::statement("
            CREATE TABLE bottega.rag_collector_documents (
                id                      BIGSERIAL PRIMARY KEY,
                document_id             VARCHAR(255) NOT NULL UNIQUE,

                -- Contenuto
                title                   TEXT NOT NULL,
                raw_text                TEXT,
                source_url              TEXT,
                source_type             VARCHAR(100) DEFAULT 'manual',

                -- Classificazione
                category                VARCHAR(100) NOT NULL,
                subcategory             VARCHAR(100),
                tags                    JSONB DEFAULT '[]',
                language                VARCHAR(5) DEFAULT 'it',

                -- Metadata Collector (tassonomia D.1.1)
                target_expertise_level  VARCHAR(50),
                target_collector_type   VARCHAR(50),
                target_medium_interest  VARCHAR(100),
                price_bracket           VARCHAR(50),
                geographic_scope        VARCHAR(100),
                metadata_extra          JSONB DEFAULT '{}',

                -- Embedding document-level (1536 dim OpenAI text-embedding-3-small)
                embedding               vector(1536),

                -- Full-text search
                search_vector           tsvector,

                -- Stato
                status                  VARCHAR(50) DEFAULT 'active',
                indexed_at              TIMESTAMP,
                created_at              TIMESTAMP DEFAULT NOW(),
                updated_at              TIMESTAMP DEFAULT NOW()
            )
        ");

        // B-tree indexes
        DB::statement('CREATE INDEX idx_rag_collector_doc_category ON bottega.rag_collector_documents(category)');
        DB::statement('CREATE INDEX idx_rag_collector_doc_status ON bottega.rag_collector_documents(status)');
        DB::statement('CREATE INDEX idx_rag_collector_doc_expertise ON bottega.rag_collector_documents(target_expertise_level)');
        DB::statement('CREATE INDEX idx_rag_collector_doc_type ON bottega.rag_collector_documents(target_collector_type)');
        DB::statement('CREATE INDEX idx_rag_collector_doc_medium ON bottega.rag_collector_documents(target_medium_interest)');
        DB::statement('CREATE INDEX idx_rag_collector_doc_price ON bottega.rag_collector_documents(price_bracket)');
        DB::statement('CREATE INDEX idx_rag_collector_doc_tags ON bottega.rag_collector_documents USING gin(tags)');

        // Full-text search GIN index
        DB::statement('CREATE INDEX idx_rag_collector_doc_fts ON bottega.rag_collector_documents USING gin(search_vector)');

        // Vector HNSW index (cosine distance)
        DB::statement('CREATE INDEX idx_rag_collector_doc_embedding ON bottega.rag_collector_documents USING hnsw (embedding vector_cosine_ops) WITH (m = 16, ef_construction = 64)');

        // Trigger: auto-update tsvector (title=A, category=B, raw_text=C)
        DB::statement("
            CREATE OR REPLACE FUNCTION bottega.rag_collector_documents_fts_trigger() RETURNS trigger AS \$\$
            BEGIN
                NEW.search_vector :=
                    setweight(to_tsvector('italian', coalesce(NEW.title, '')), 'A') ||
                    setweight(to_tsvector('italian', coalesce(NEW.category, '')), 'B') ||
                    setweight(to_tsvector('italian', coalesce(NEW.raw_text, '')), 'C');
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql
        ");

        DB::statement("
            CREATE TRIGGER rag_collector_documents_fts_update
                BEFORE INSERT OR UPDATE OF title, category, raw_text
                ON bottega.rag_collector_documents
                FOR EACH ROW
                EXECUTE FUNCTION bottega.rag_collector_documents_fts_trigger()
        ");

        // Trigger: auto-update updated_at
        DB::statement("
            CREATE OR REPLACE FUNCTION bottega.rag_collector_documents_updated_at() RETURNS trigger AS \$\$
            BEGIN
                NEW.updated_at = NOW();
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql
        ");

        DB::statement("
            CREATE TRIGGER rag_collector_documents_updated_at
                BEFORE UPDATE ON bottega.rag_collector_documents
                FOR EACH ROW
                EXECUTE FUNCTION bottega.rag_collector_documents_updated_at()
        ");
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS rag_collector_documents_updated_at ON bottega.rag_collector_documents');
        DB::statement('DROP TRIGGER IF EXISTS rag_collector_documents_fts_update ON bottega.rag_collector_documents');
        DB::statement('DROP FUNCTION IF EXISTS bottega.rag_collector_documents_updated_at()');
        DB::statement('DROP FUNCTION IF EXISTS bottega.rag_collector_documents_fts_trigger()');
        DB::statement('DROP TABLE IF EXISTS bottega.rag_collector_documents CASCADE');
    }
};
