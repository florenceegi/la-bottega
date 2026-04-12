<?php

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Tracking completamento step nei percorsi Creator
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StepCompletion extends Model
{
    protected $table = 'bottega.step_completions';

    protected $fillable = [
        'artist_profile_id',
        'percorso',
        'fase',
        'step_number',
        'status',
        'completed_at',
        'verified_at',
        'verification_data',
        'maestro_notes',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'verified_at' => 'datetime',
            'verification_data' => 'array',
            'maestro_notes' => 'array',
        ];
    }

    public function artistProfile(): BelongsTo
    {
        return $this->belongsTo(ArtistProfile::class, 'artist_profile_id');
    }
}
