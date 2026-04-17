<?php

declare(strict_types=1);

/**
 * @package App\Services\Tools
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Market Pulse — strumento C.2 La Bottega. Incrocia segnali di mercato
 *          (bottega.market_trends) con vendite artista da EGI. Struttura funzionante
 *          anche con zero vendite o tabella segnali vuota.
 */

namespace App\Services\Tools;

use App\Models\ArtistProfile;
use App\Models\MarketTrend;
use App\Services\ApiClients\EgiApiClient;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

class MarketPulseService
{
    private const CAREER_EMERGING_MAX = 30;
    private const CAREER_MID_MAX = 60;

    public function __construct(
        private readonly EgiApiClient $egiClient,
        private readonly UltraLogManager $logger,
        private readonly ErrorManagerInterface $errorManager,
    ) {}

    public function pulse(ArtistProfile $profile): array
    {
        try {
            $careerLevel = $this->deriveCareerLevel($profile->profile_completeness_score ?? 0);
            $medium = $profile->medium_primary;

            $signals = MarketTrend::active()
                ->forMedium($medium)
                ->forCareerLevel($careerLevel)
                ->orderByDesc('updated_at')
                ->limit(20)
                ->get()
                ->map(fn (MarketTrend $t) => $this->formatTrend($t))
                ->all();

            $salesSummary = $this->buildSalesSummary($profile->user_id, $medium);

            $this->logger->info('Market Pulse generated', [
                'user_id' => $profile->user_id,
                'career_level' => $careerLevel,
                'medium' => $medium,
                'signals_count' => count($signals),
                'sales_count' => $salesSummary['sales_count'],
            ]);

            return [
                'medium_primary' => $medium,
                'career_level' => $careerLevel,
                'signals_count' => count($signals),
                'signals' => $signals,
                'sales_summary' => $salesSummary,
                'analyzed_at' => now()->toIso8601String(),
            ];
        } catch (\Exception $e) {
            $this->errorManager->handle('BOTTEGA_MARKET_PULSE_ERROR', [
                'user_id' => $profile->user_id,
            ], $e);

            return [
                'medium_primary' => null,
                'career_level' => 'emerging',
                'signals_count' => 0,
                'signals' => [],
                'sales_summary' => $this->emptySalesSummary(),
                'error' => __('bottega.market_pulse_error'),
                'analyzed_at' => now()->toIso8601String(),
            ];
        }
    }

    private function buildSalesSummary(int $userId, ?string $medium): array
    {
        try {
            $history = $this->egiClient->getSalesHistory($userId);
            $sales = is_array($history) ? ($history['data'] ?? $history) : [];
        } catch (\Exception $e) {
            $this->logger->warning('EGI sales history fetch failed, returning empty summary', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            return $this->emptySalesSummary();
        }

        if (!is_array($sales) || empty($sales)) {
            return $this->emptySalesSummary();
        }

        $totalCount = 0;
        $totalAmount = 0.0;
        $byMedium = [];
        $lastDate = null;

        foreach ($sales as $sale) {
            if (!is_array($sale)) {
                continue;
            }
            $totalCount++;
            $amount = (float) ($sale['amount'] ?? $sale['price'] ?? 0);
            $totalAmount += $amount;
            $saleMedium = $sale['medium'] ?? $sale['egi_medium'] ?? 'unknown';
            if (!isset($byMedium[$saleMedium])) {
                $byMedium[$saleMedium] = ['count' => 0, 'amount' => 0.0];
            }
            $byMedium[$saleMedium]['count']++;
            $byMedium[$saleMedium]['amount'] += $amount;
            $date = $sale['sold_at'] ?? $sale['created_at'] ?? null;
            if ($date && (!$lastDate || $date > $lastDate)) {
                $lastDate = $date;
            }
        }

        $breakdown = [];
        foreach ($byMedium as $m => $data) {
            $breakdown[] = [
                'medium' => $m,
                'count' => $data['count'],
                'amount' => round($data['amount'], 2),
                'is_primary' => $medium !== null && $m === $medium,
            ];
        }
        usort($breakdown, fn ($a, $b) => $b['count'] <=> $a['count']);

        return [
            'has_data' => $totalCount > 0,
            'sales_count' => $totalCount,
            'total_amount' => round($totalAmount, 2),
            'last_sale_at' => $lastDate,
            'by_medium' => $breakdown,
        ];
    }

    private function emptySalesSummary(): array
    {
        return [
            'has_data' => false,
            'sales_count' => 0,
            'total_amount' => 0.0,
            'last_sale_at' => null,
            'by_medium' => [],
        ];
    }

    private function formatTrend(MarketTrend $t): array
    {
        return [
            'id' => $t->id,
            'signal_key' => $t->signal_key,
            'category' => $t->category,
            'medium' => $t->medium,
            'career_level' => $t->career_level,
            'region' => $t->region,
            'direction' => $t->direction,
            'magnitude' => $t->magnitude,
            'insight' => $t->insight,
            'actionable_advice' => $t->actionable_advice,
            'source' => $t->source,
            'observed_from' => $t->observed_from?->toDateString(),
            'observed_to' => $t->observed_to?->toDateString(),
        ];
    }

    private function deriveCareerLevel(int $completenessScore): string
    {
        if ($completenessScore <= self::CAREER_EMERGING_MAX) {
            return 'emerging';
        }
        if ($completenessScore <= self::CAREER_MID_MAX) {
            return 'mid';
        }
        return 'established';
    }
}
