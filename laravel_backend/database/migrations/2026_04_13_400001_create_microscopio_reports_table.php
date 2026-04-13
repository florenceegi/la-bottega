<?php

declare(strict_types=1);

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Tabella storico report Microscopio — persiste ogni diagnosi per confronto progressi.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE bottega.microscopio_reports (
                id                      BIGSERIAL PRIMARY KEY,
                artist_profile_id       BIGINT NOT NULL REFERENCES bottega.artist_profiles(id) ON DELETE CASCADE,
                total_score             INTEGER NOT NULL DEFAULT 0,
                scores                  JSONB NOT NULL DEFAULT '{}',
                findings                JSONB NOT NULL DEFAULT '[]',
                findings_count          INTEGER NOT NULL DEFAULT 0,
                traits_coherence        NUMERIC(3,2),
                weak_descriptions_count INTEGER NOT NULL DEFAULT 0,
                recommendations         JSONB NOT NULL DEFAULT '[]',
                analyzed_at             TIMESTAMP NOT NULL DEFAULT NOW(),
                created_at              TIMESTAMP DEFAULT NOW()
            )
        ");

        DB::statement('CREATE INDEX idx_microscopio_profile ON bottega.microscopio_reports(artist_profile_id)');
        DB::statement('CREATE INDEX idx_microscopio_analyzed ON bottega.microscopio_reports(analyzed_at DESC)');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS bottega.microscopio_reports CASCADE');
    }
};
