<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Log esecuzioni strumenti — prerequisito GAP 1 sidebar scorciatoie
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bottega.tool_executions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('tool_name', 50);
            $table->jsonb('input_data')->nullable();
            $table->jsonb('output_data')->nullable();
            $table->unsignedInteger('egili_cost')->default(0);
            $table->unsignedInteger('duration_ms')->default(0);
            $table->boolean('success')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('core.users')->cascadeOnDelete();

            $table->index(['user_id', 'tool_name']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bottega.tool_executions');
    }
};
