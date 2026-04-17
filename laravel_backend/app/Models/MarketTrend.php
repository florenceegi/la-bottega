<?php

declare(strict_types=1);

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Market trend — segnale di mercato per Market Pulse (C.2).
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MarketTrend extends Model
{
    protected $table = 'bottega.market_trends';

    protected $fillable = [
        'signal_key',
        'category',
        'medium',
        'career_level',
        'region',
        'direction',
        'magnitude',
        'insight',
        'actionable_advice',
        'source',
        'observed_from',
        'observed_to',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'observed_from' => 'date',
            'observed_to' => 'date',
            'active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeForMedium(Builder $query, ?string $medium): Builder
    {
        if (!$medium) {
            return $query;
        }
        return $query->where(function ($q) use ($medium) {
            $q->where('medium', $medium)->orWhereNull('medium');
        });
    }

    public function scopeForCareerLevel(Builder $query, ?string $level): Builder
    {
        if (!$level) {
            return $query;
        }
        return $query->where(function ($q) use ($level) {
            $q->where('career_level', $level)->orWhereNull('career_level');
        });
    }
}
