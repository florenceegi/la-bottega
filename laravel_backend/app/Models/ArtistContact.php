<?php

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Pipeline contatti artista — tracking relazioni con gallerie, curatori, eventi
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtistContact extends Model
{
    protected $table = 'bottega.artist_contacts';

    protected $fillable = [
        'artist_profile_id',
        'contact_name',
        'contact_type',
        'stage',
        'notes',
        'last_contact_at',
        'next_followup_at',
    ];

    protected function casts(): array
    {
        return [
            'last_contact_at' => 'datetime',
            'next_followup_at' => 'datetime',
        ];
    }

    public function artistProfile(): BelongsTo
    {
        return $this->belongsTo(ArtistProfile::class, 'artist_profile_id');
    }
}
