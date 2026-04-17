<?php

declare(strict_types=1);

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Market trends — segnali di mercato per Market Pulse (C.2).
 *          Struttura generica: funziona anche con tabella vuota, popola nel tempo.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bottega.market_trends', function (Blueprint $table) {
            $table->id();
            $table->string('signal_key', 100);
            $table->string('category', 50);
            $table->string('medium', 50)->nullable();
            $table->string('career_level', 20)->nullable();
            $table->string('region', 3)->nullable();
            $table->string('direction', 20);
            $table->string('magnitude', 20)->nullable();
            $table->text('insight');
            $table->text('actionable_advice')->nullable();
            $table->string('source', 255)->nullable();
            $table->date('observed_from')->nullable();
            $table->date('observed_to')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('category');
            $table->index('medium');
            $table->index('active');
            $table->unique('signal_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bottega.market_trends');
    }
};
