<?php

declare(strict_types=1);

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Tabella RAG Creator — documenti sorgente per il Maestro di Bottega.
 *          Contiene: casi studio, guide marketing artistico, best practice,
 *          logiche pricing, analisi di mercato. Embeddings vector(1536) + FTS italiano + HNSW.
 *          Schema: bottega.rag_creator_documents
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector');

        DB::statement("
            CREATE TABLE bottega.rag_creator_documents (
                id                  BIGSERIAL PRIMARY KEY,
                document_id         VARCHAR(255) NOT NULL UNIQUE,

                -- Contenuto
                title               TEXT NOT NULL,
                raw_text            TEXT,
                source_url          TEXT,
                source_type         VARCHAR(100) DEFAULT 'manual',

                -- Classificazione
                category            VARCHAR(100) NOT NULL,
                subcategory         VARCHAR(100),
                tags                JSONB DEFAULT '[]',
                language            VARCHAR(5) DEFAULT 'it',

                -- Metadata artistica
                target_medium       VARCHAR(100),
                target_career_level VARCHAR(50),
                target_percorso     VARCHAR(50),
                geographic_scope    VARCHAR(100),
                metadata_extra      JSONB DEFAULT '{}',

                -- Embedding document-level (1536 dim OpenAI text-embedding-3-small)
                embedding           vector(1536),

                -- Full-text search
                search_vector       tsvector,

                -- Stato
                status              VARCHAR(50) DEFAULT 'active',
                indexed_at          TIMESTAMP,
                created_at          TIMESTAMP DEFAULT NOW(),
                updated_at          TIMESTAMP DEFAULT NOW()
            )
        ");

        // B-tree indexes
        DB::statement('CREATE INDEX idx_rag_creator_doc_category ON bottega.rag_creator_documents(category)');
        DB::statement('CREATE INDEX idx_rag_creator_doc_status ON bottega.rag_creator_documents(status)');
        DB::statement('CREATE INDEX idx_rag_creator_doc_medium ON bottega.rag_creator_documents(target_medium)');
        DB::statement('CREATE INDEX idx_rag_creator_doc_level ON bottega.rag_creator_documents(target_career_level)');
        DB::statement('CREATE INDEX idx_rag_creator_doc_percorso ON bottega.rag_creator_documents(target_percorso)');
        DB::statement('CREATE INDEX idx_rag_creator_doc_tags ON bottega.rag_creator_documents USING gin(tags)');

        // Full-text search GIN index
        DB::statement('CREATE INDEX idx_rag_creator_doc_fts ON bottega.rag_creator_documents USING gin(search_vector)');

        // Vector HNSW index (cosine distance)
        DB::statement('CREATE INDEX idx_rag_creator_doc_embedding ON bottega.rag_creator_documents USING hnsw (embedding vector_cosine_ops) WITH (m = 16, ef_construction = 64)');

        // Trigger: auto-update tsvector (title=A, category=B, raw_text=C)
        DB::statement("
            CREATE OR REPLACE FUNCTION bottega.rag_creator_documents_fts_trigger() RETURNS trigger AS \$\$
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
            CREATE TRIGGER rag_creator_documents_fts_update
                BEFORE INSERT OR UPDATE OF title, category, raw_text
                ON bottega.rag_creator_documents
                FOR EACH ROW
                EXECUTE FUNCTION bottega.rag_creator_documents_fts_trigger()
        ");

        // Trigger: auto-update updated_at
        DB::statement("
            CREATE OR REPLACE FUNCTION bottega.rag_creator_documents_updated_at() RETURNS trigger AS \$\$
            BEGIN
                NEW.updated_at = NOW();
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql
        ");

        DB::statement("
            CREATE TRIGGER rag_creator_documents_updated_at
                BEFORE UPDATE ON bottega.rag_creator_documents
                FOR EACH ROW
                EXECUTE FUNCTION bottega.rag_creator_documents_updated_at()
        ");
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS rag_creator_documents_updated_at ON bottega.rag_creator_documents');
        DB::statement('DROP TRIGGER IF EXISTS rag_creator_documents_fts_update ON bottega.rag_creator_documents');
        DB::statement('DROP FUNCTION IF EXISTS bottega.rag_creator_documents_updated_at()');
        DB::statement('DROP FUNCTION IF EXISTS bottega.rag_creator_documents_fts_trigger()');
        DB::statement('DROP TABLE IF EXISTS bottega.rag_creator_documents CASCADE');
    }
};
