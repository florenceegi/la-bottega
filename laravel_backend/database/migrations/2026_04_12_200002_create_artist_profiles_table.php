<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Profilo Bottega dell'artista — estensione di core.users
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bottega.artist_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('medium_primary', 50)->nullable();
            $table->text('artist_statement_short')->nullable();
            $table->string('market_segment_primary', 50)->nullable();
            $table->string('instagram_username', 100)->nullable();
            $table->string('email_tool', 50)->nullable();
            $table->unsignedInteger('email_list_count')->default(0);
            $table->unsignedInteger('instagram_weeks_active')->default(0);
            $table->string('percorso_current', 20)->nullable();
            $table->timestamp('percorso_started_at')->nullable();
            $table->timestamp('percorso_completed_at')->nullable();
            $table->unsignedSmallInteger('profile_completeness_score')->default(0);
            $table->unsignedSmallInteger('coherence_score')->default(0);
            $table->unsignedSmallInteger('credibility_score')->default(0);
            $table->timestamp('onboarding_completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('core.users')->cascadeOnDelete();

            $table->index('percorso_current');
            $table->index('medium_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bottega.artist_profiles');
    }
};
