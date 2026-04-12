<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Tracking completamento step nei percorsi Creator
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bottega.step_completions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('artist_profile_id');
            $table->string('percorso', 20);
            $table->unsignedSmallInteger('fase');
            $table->unsignedSmallInteger('step_number');
            $table->string('status', 20)->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->jsonb('verification_data')->nullable();
            $table->jsonb('maestro_notes')->nullable();
            $table->timestamps();

            $table->foreign('artist_profile_id')
                ->references('id')->on('bottega.artist_profiles')
                ->cascadeOnDelete();

            $table->unique(['artist_profile_id', 'percorso', 'fase', 'step_number'], 'step_completions_unique');
            $table->index(['percorso', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bottega.step_completions');
    }
};
