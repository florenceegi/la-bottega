<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Memoria strutturata e narrativa del Maestro per ogni utente
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bottega.maestro_memory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('memory_type', 20);
            $table->string('key', 255);
            $table->jsonb('value');
            $table->string('source', 100)->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->decimal('relevance_score', 4, 2)->default(1.00);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('core.users')->cascadeOnDelete();

            $table->unique(['user_id', 'memory_type', 'key'], 'maestro_memory_unique');
            $table->index(['user_id', 'relevance_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bottega.maestro_memory');
    }
};
