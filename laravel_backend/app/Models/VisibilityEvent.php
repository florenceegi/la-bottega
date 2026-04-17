<?php

declare(strict_types=1);

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Model bottega.visibility_events — eventi comportamentali visitor-artist.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisibilityEvent extends Model
{
    protected $table = 'bottega.visibility_events';

    protected $fillable = [
        'artist_user_id',
        'event_type',
        'egi_id',
        'collection_id',
        'visitor_user_id',
        'referrer',
        'country',
        'metadata',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
        'artist_user_id' => 'integer',
        'egi_id' => 'integer',
        'collection_id' => 'integer',
        'visitor_user_id' => 'integer',
    ];

    public const EVENT_PROFILE_VIEW = 'profile_view';
    public const EVENT_EGI_VIEW = 'egi_view';
    public const EVENT_COLLECTION_VIEW = 'collection_view';
    public const EVENT_BIO_READ = 'bio_read';
    public const EVENT_EGI_FAVORITE = 'egi_favorite';
    public const EVENT_COA_VERIFY = 'coa_verify';
    public const EVENT_EGI_PURCHASE = 'egi_purchase';

    public const STAGE_AWARENESS = ['profile_view', 'collection_view'];
    public const STAGE_INTEREST = ['egi_view', 'bio_read'];
    public const STAGE_CONSIDERATION = ['egi_favorite', 'coa_verify'];
    public const STAGE_CONVERSION = ['egi_purchase'];

    public function scopeForArtist($query, int $userId)
    {
        return $query->where('artist_user_id', $userId);
    }

    public function scopeBetween($query, $from, $to)
    {
        return $query->whereBetween('occurred_at', [$from, $to]);
    }

    public function scopeOfType($query, string|array $type)
    {
        return is_array($type)
            ? $query->whereIn('event_type', $type)
            : $query->where('event_type', $type);
    }
}
