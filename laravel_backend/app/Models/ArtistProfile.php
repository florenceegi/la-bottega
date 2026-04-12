<?php

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Profilo Bottega dell'artista — estensione di core.users
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArtistProfile extends Model
{
    protected $table = 'bottega.artist_profiles';

    protected $fillable = [
        'user_id',
        'medium_primary',
        'artist_statement_short',
        'market_segment_primary',
        'instagram_username',
        'email_tool',
        'email_list_count',
        'instagram_weeks_active',
        'percorso_current',
        'percorso_started_at',
        'percorso_completed_at',
        'profile_completeness_score',
        'coherence_score',
        'credibility_score',
        'onboarding_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'percorso_started_at' => 'datetime',
            'percorso_completed_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function stepCompletions(): HasMany
    {
        return $this->hasMany(StepCompletion::class, 'artist_profile_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ArtistContact::class, 'artist_profile_id');
    }
}
