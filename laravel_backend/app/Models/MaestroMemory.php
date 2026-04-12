<?php

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Memoria strutturata e narrativa del Maestro per ogni utente
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaestroMemory extends Model
{
    protected $table = 'bottega.maestro_memory';

    protected $fillable = [
        'user_id',
        'memory_type',
        'key',
        'value',
        'source',
        'last_read_at',
        'relevance_score',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'array',
            'last_read_at' => 'datetime',
            'relevance_score' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
