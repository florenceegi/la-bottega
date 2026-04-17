<?php

declare(strict_types=1);

/**
 * @package Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Tabella bottega.visibility_events — eventi analytics per Visibility Tracker C.3.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bottega.visibility_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('artist_user_id');
            $table->string('event_type', 50);
            $table->unsignedBigInteger('egi_id')->nullable();
            $table->unsignedBigInteger('collection_id')->nullable();
            $table->unsignedBigInteger('visitor_user_id')->nullable();
            $table->string('referrer', 255)->nullable();
            $table->string('country', 3)->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();

            $table->index('artist_user_id');
            $table->index('event_type');
            $table->index('occurred_at');
            $table->index(['artist_user_id', 'event_type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bottega.visibility_events');
    }
};
