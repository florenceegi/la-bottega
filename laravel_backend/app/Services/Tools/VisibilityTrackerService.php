<?php

declare(strict_types=1);

/**
 * @package App\Services\Tools
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Visibility Tracker — strumento C.3 La Bottega. Aggrega eventi comportamentali
 *          artist-side in funnel (awareness → interest → consideration → conversion)
 *          con confronto 7d vs 7d precedenti. Struttura funzionante con zero eventi.
 */

namespace App\Services\Tools;

use App\Models\ArtistProfile;
use App\Models\VisibilityEvent;
use Illuminate\Support\Carbon;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

class VisibilityTrackerService
{
    public function __construct(
        private readonly UltraLogManager $logger,
        private readonly ErrorManagerInterface $errorManager,
    ) {}

    public function report(ArtistProfile $profile, int $days = 7): array
    {
        try {
            $userId = $profile->user_id;
            $now = Carbon::now();
            $currentFrom = $now->copy()->subDays($days)->startOfDay();
            $currentTo = $now->copy();
            $priorFrom = $now->copy()->subDays($days * 2)->startOfDay();
            $priorTo = $now->copy()->subDays($days);

            $currentCounts = $this->countByType($userId, $currentFrom, $currentTo);
            $priorCounts = $this->countByType($userId, $priorFrom, $priorTo);

            $funnel = $this->buildFunnel($currentCounts, $priorCounts);
            $breakdown = $this->buildBreakdown($userId, $currentFrom, $currentTo);
            $referrers = $this->buildReferrers($userId, $currentFrom, $currentTo);
            $countries = $this->buildCountries($userId, $currentFrom, $currentTo);

            $totalCurrent = array_sum($currentCounts);
            $totalPrior = array_sum($priorCounts);

            $this->logger->info('Visibility Tracker generated', [
                'user_id' => $userId,
                'window_days' => $days,
                'total_events_current' => $totalCurrent,
                'total_events_prior' => $totalPrior,
            ]);

            return [
                'window_days' => $days,
                'period_from' => $currentFrom->toIso8601String(),
                'period_to' => $currentTo->toIso8601String(),
                'total_events' => $totalCurrent,
                'total_events_prior' => $totalPrior,
                'delta_pct' => $this->deltaPct($totalCurrent, $totalPrior),
                'has_data' => $totalCurrent > 0,
                'funnel' => $funnel,
                'events_breakdown' => $breakdown,
                'top_referrers' => $referrers,
                'top_countries' => $countries,
                'analyzed_at' => $now->toIso8601String(),
            ];
        } catch (\Exception $e) {
            $this->errorManager->handle('BOTTEGA_VISIBILITY_TRACKER_ERROR', [
                'user_id' => $profile->user_id,
            ], $e);

            return $this->emptyReport($days);
        }
    }

    private function countByType(int $userId, Carbon $from, Carbon $to): array
    {
        $rows = VisibilityEvent::forArtist($userId)
            ->between($from, $to)
            ->selectRaw('event_type, COUNT(*) as c')
            ->groupBy('event_type')
            ->pluck('c', 'event_type')
            ->toArray();

        return array_map('intval', $rows);
    }

    private function buildFunnel(array $current, array $prior): array
    {
        $stages = [
            'awareness' => VisibilityEvent::STAGE_AWARENESS,
            'interest' => VisibilityEvent::STAGE_INTEREST,
            'consideration' => VisibilityEvent::STAGE_CONSIDERATION,
            'conversion' => VisibilityEvent::STAGE_CONVERSION,
        ];

        $result = [];
        foreach ($stages as $key => $types) {
            $currentCount = array_sum(array_intersect_key($current, array_flip($types)));
            $priorCount = array_sum(array_intersect_key($prior, array_flip($types)));
            $result[] = [
                'stage' => $key,
                'count' => $currentCount,
                'count_prior' => $priorCount,
                'delta_pct' => $this->deltaPct($currentCount, $priorCount),
                'event_types' => $types,
            ];
        }
        return $result;
    }

    private function buildBreakdown(int $userId, Carbon $from, Carbon $to): array
    {
        $counts = $this->countByType($userId, $from, $to);
        $breakdown = [];
        foreach ($counts as $type => $count) {
            $breakdown[] = ['event_type' => $type, 'count' => $count];
        }
        usort($breakdown, fn ($a, $b) => $b['count'] <=> $a['count']);
        return $breakdown;
    }

    private function buildReferrers(int $userId, Carbon $from, Carbon $to): array
    {
        return VisibilityEvent::forArtist($userId)
            ->between($from, $to)
            ->whereNotNull('referrer')
            ->selectRaw('referrer, COUNT(*) as c')
            ->groupBy('referrer')
            ->orderByDesc('c')
            ->limit(5)
            ->get()
            ->map(fn ($r) => ['referrer' => $r->referrer, 'count' => (int) $r->c])
            ->all();
    }

    private function buildCountries(int $userId, Carbon $from, Carbon $to): array
    {
        return VisibilityEvent::forArtist($userId)
            ->between($from, $to)
            ->whereNotNull('country')
            ->selectRaw('country, COUNT(*) as c')
            ->groupBy('country')
            ->orderByDesc('c')
            ->limit(5)
            ->get()
            ->map(fn ($r) => ['country' => $r->country, 'count' => (int) $r->c])
            ->all();
    }

    private function deltaPct(int $current, int $prior): ?float
    {
        if ($prior === 0) {
            return $current > 0 ? 100.0 : null;
        }
        return round((($current - $prior) / $prior) * 100, 1);
    }

    private function emptyReport(int $days): array
    {
        $now = Carbon::now();
        return [
            'window_days' => $days,
            'period_from' => $now->copy()->subDays($days)->toIso8601String(),
            'period_to' => $now->toIso8601String(),
            'total_events' => 0,
            'total_events_prior' => 0,
            'delta_pct' => null,
            'has_data' => false,
            'funnel' => [
                ['stage' => 'awareness', 'count' => 0, 'count_prior' => 0, 'delta_pct' => null, 'event_types' => VisibilityEvent::STAGE_AWARENESS],
                ['stage' => 'interest', 'count' => 0, 'count_prior' => 0, 'delta_pct' => null, 'event_types' => VisibilityEvent::STAGE_INTEREST],
                ['stage' => 'consideration', 'count' => 0, 'count_prior' => 0, 'delta_pct' => null, 'event_types' => VisibilityEvent::STAGE_CONSIDERATION],
                ['stage' => 'conversion', 'count' => 0, 'count_prior' => 0, 'delta_pct' => null, 'event_types' => VisibilityEvent::STAGE_CONVERSION],
            ],
            'events_breakdown' => [],
            'top_referrers' => [],
            'top_countries' => [],
            'error' => __('bottega.visibility_tracker_error'),
            'analyzed_at' => $now->toIso8601String(),
        ];
    }
}
