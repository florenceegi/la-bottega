<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Profilo Bottega del collezionista — estensione di core.users
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bottega.collector_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedInteger('budget_range_min')->nullable();
            $table->unsignedInteger('budget_range_max')->nullable();
            $table->jsonb('preferred_mediums')->nullable();
            $table->jsonb('preferred_styles')->nullable();
            $table->string('collection_goal', 255)->nullable();
            $table->timestamp('onboarding_completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('core.users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bottega.collector_profiles');
    }
};
