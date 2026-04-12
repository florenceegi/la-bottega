<?php

/**
 * @package App\Database\Migrations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Pipeline contatti artista — tracking relazioni con gallerie, curatori, eventi
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bottega.artist_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('artist_profile_id');
            $table->string('contact_name', 255);
            $table->string('contact_type', 50);
            $table->string('stage', 30)->default('identified');
            $table->text('notes')->nullable();
            $table->timestamp('last_contact_at')->nullable();
            $table->timestamp('next_followup_at')->nullable();
            $table->timestamps();

            $table->foreign('artist_profile_id')
                ->references('id')->on('bottega.artist_profiles')
                ->cascadeOnDelete();

            $table->index(['artist_profile_id', 'stage']);
            $table->index('next_followup_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bottega.artist_contacts');
    }
};
