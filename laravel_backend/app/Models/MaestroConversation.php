<?php

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Storico conversazioni con il Maestro di Bottega
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaestroConversation extends Model
{
    protected $table = 'bottega.maestro_conversations';

    protected $fillable = [
        'user_id',
        'instance',
        'session_id',
        'message',
        'role',
        'context_data',
        'tokens_used',
        'model_used',
    ];

    protected function casts(): array
    {
        return [
            'context_data' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
