<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Opportunita esterne per artisti (Binocolo) — call for artists, fiere, residenze
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bottega.opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('type', 50);
            $table->date('deadline')->nullable();
            $table->string('url', 500)->nullable();
            $table->jsonb('requirements')->nullable();
            $table->jsonb('mediums_accepted')->nullable();
            $table->string('career_level_min', 20)->nullable();
            $table->string('career_level_max', 20)->nullable();
            $table->string('country', 3)->nullable();
            $table->text('description')->nullable();
            $table->string('source', 255)->nullable();
            $table->boolean('verified')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('type');
            $table->index('deadline');
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bottega.opportunities');
    }
};
