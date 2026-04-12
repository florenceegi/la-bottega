<?php

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Opportunita esterne per artisti — Binocolo strumento
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Opportunity extends Model
{
    protected $table = 'bottega.opportunities';

    protected $fillable = [
        'title',
        'type',
        'deadline',
        'url',
        'requirements',
        'mediums_accepted',
        'career_level_min',
        'career_level_max',
        'country',
        'description',
        'source',
        'verified',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'requirements' => 'array',
            'mediums_accepted' => 'array',
            'verified' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('deadline', '>=', now());
    }
}
