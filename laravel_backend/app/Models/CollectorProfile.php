<?php

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Profilo Bottega del collezionista — estensione di core.users
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectorProfile extends Model
{
    protected $table = 'bottega.collector_profiles';

    protected $fillable = [
        'user_id',
        'budget_range_min',
        'budget_range_max',
        'preferred_mediums',
        'preferred_styles',
        'collection_goal',
        'onboarding_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'preferred_mediums' => 'array',
            'preferred_styles' => 'array',
            'onboarding_completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
