<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Storico conversazioni con il Maestro di Bottega (Creator e Collector)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bottega.maestro_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('instance', 20);
            $table->uuid('session_id')->index();
            $table->text('message');
            $table->string('role', 20);
            $table->jsonb('context_data')->nullable();
            $table->unsignedInteger('tokens_used')->default(0);
            $table->string('model_used', 50)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('core.users')->cascadeOnDelete();

            $table->index(['user_id', 'instance']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bottega.maestro_conversations');
    }
};
