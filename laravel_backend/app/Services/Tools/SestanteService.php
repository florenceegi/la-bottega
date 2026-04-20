<?php

declare(strict_types=1);

/**
 * @package App\Services\Tools
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-20
 * @purpose Sestante — positioning artista vs comparabili su piattaforma FlorenceEGI.
 *          Percentile prezzo + visibilita, gap vs mediana, 3 comparabili anonimizzati.
 *          Struttura funzionante con zero comparables (emptyPositioning).
 */

namespace App\Services\Tools;

use App\Models\ArtistProfile;
use App\Services\ApiClients\EgiApiClient;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

class SestanteService
{
    private const CAREER_EMERGING_MAX = 30;
    private const CAREER_MID_MAX = 60;
    private const MIN_COMPARABLES = 3;
    private const COMPARABLES_LIMIT = 50;
    private const SALES_WINDOW_DAYS = 365;

    public function __construct(
        private readonly EgiApiClient $egiClient,
        private readonly UltraLogManager $logger,
        private readonly ErrorManagerInterface $errorManager,
    ) {}

    public function position(ArtistProfile $profile): array
    {
        try {
            $careerLevel = $this->deriveCareerLevel($profile->profile_completeness_score ?? 0);
            $medium = $profile->medium_primary;
            $userId = $profile->user_id;

            $ownStats = $this->buildOwnStats($userId, $medium, $profile->profile_completeness_score ?? 0);
            $comparables = $this->fetchComparables($userId, $medium, $careerLevel);

            if (count($comparables) < self::MIN_COMPARABLES) {
                return $this->emptyPositioning($medium, $careerLevel, $ownStats, count($comparables));
            }

            $metrics = $this->computeMetrics($ownStats, $comparables);
            $topAnon = $this->anonymizeTopComparables($comparables, 3);

            $this->logger->info('Sestante positioning generated', [
                'user_id' => $userId,
                'medium' => $medium,
                'career_level' => $careerLevel,
                'comparables_count' => count($comparables),
            ]);

            return [
                'has_data' => true,
                'medium_primary' => $medium,
                'career_level' => $careerLevel,
                'comparables_count' => count($comparables),
                'own_stats' => $ownStats,
                'metrics' => $metrics,
                'top_comparables' => $topAnon,
                'analyzed_at' => now()->toIso8601String(),
            ];
        } catch (\Exception $e) {
            $this->errorManager->handle('BOTTEGA_SESTANTE_ERROR', [
                'user_id' => $profile->user_id,
            ], $e);

            return $this->emptyPositioning(
                $profile->medium_primary,
                $this->deriveCareerLevel($profile->profile_completeness_score ?? 0),
                $this->emptyOwnStats(),
                0,
                __('bottega.sestante_error'),
            );
        }
    }

    private function buildOwnStats(int $userId, ?string $medium, int $completenessScore): array
    {
        $egis = $this->safeFetchEgis($userId);
        $prices = [];
        foreach ($egis as $egi) {
            $p = $this->floatOrNull($egi['price'] ?? $egi['price_eur'] ?? null);
            if ($p !== null && $p > 0) {
                $prices[] = $p;
            }
        }

        $sales = $this->safeFetchSales($userId);
        $recentCount = 0;
        $cutoff = now()->subDays(self::SALES_WINDOW_DAYS);
        foreach ($sales as $sale) {
            $date = $sale['sold_at'] ?? $sale['created_at'] ?? null;
            if ($date && $date >= $cutoff->toDateTimeString()) {
                $recentCount++;
            }
        }

        return [
            'egi_count' => count($egis),
            'avg_price' => count($prices) > 0 ? round(array_sum($prices) / count($prices), 2) : null,
            'min_price' => count($prices) > 0 ? min($prices) : null,
            'max_price' => count($prices) > 0 ? max($prices) : null,
            'sales_count_year' => $recentCount,
            'profile_completeness' => $completenessScore,
            'medium' => $medium,
        ];
    }

    private function fetchComparables(int $userId, ?string $medium, string $careerLevel): array
    {
        try {
            $response = $this->egiClient->getComparableArtists([
                'medium' => $medium,
                'career_level' => $careerLevel,
                'exclude_user_id' => $userId,
                'limit' => self::COMPARABLES_LIMIT,
            ]);
        } catch (\Exception $e) {
            $this->logger->warning('EGI getComparableArtists failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            return [];
        }

        $items = is_array($response) ? ($response['data'] ?? $response) : [];
        if (!is_array($items)) {
            return [];
        }

        $clean = [];
        foreach ($items as $item) {
            if (!is_array($item)) continue;
            $avgPrice = $this->floatOrNull($item['avg_price_eur'] ?? $item['avg_price'] ?? null);
            if ($avgPrice === null) continue;
            $clean[] = [
                'avg_price' => $avgPrice,
                'egi_count' => (int) ($item['egi_count'] ?? 0),
                'sales_count_year' => (int) ($item['sales_count_year'] ?? 0),
                'profile_completeness' => (int) ($item['profile_completeness'] ?? 0),
                'medium' => $item['medium_primary'] ?? $item['medium'] ?? null,
                'career_level' => $item['career_level'] ?? null,
            ];
        }
        return $clean;
    }

    private function computeMetrics(array $ownStats, array $comparables): array
    {
        $prices = array_column($comparables, 'avg_price');
        $sales = array_column($comparables, 'sales_count_year');
        $egiCounts = array_column($comparables, 'egi_count');

        $medianPrice = $this->median($prices);
        $medianSales = $this->median($sales);
        $medianEgis = $this->median($egiCounts);

        $ownPrice = $ownStats['avg_price'];
        $ownSales = $ownStats['sales_count_year'];
        $ownEgis = $ownStats['egi_count'];

        $priceGapPct = ($ownPrice !== null && $medianPrice > 0)
            ? round((($ownPrice - $medianPrice) / $medianPrice) * 100, 1)
            : null;

        return [
            'percentile_price' => $ownPrice !== null ? $this->percentile($prices, $ownPrice) : null,
            'percentile_visibility' => $this->percentile($sales, $ownSales),
            'percentile_portfolio' => $this->percentile($egiCounts, $ownEgis),
            'median_price_comparables' => $medianPrice,
            'median_sales_comparables' => $medianSales,
            'median_egi_count_comparables' => $medianEgis,
            'price_gap_pct' => $priceGapPct,
        ];
    }

    private function anonymizeTopComparables(array $comparables, int $topN): array
    {
        usort($comparables, fn ($a, $b) => $b['avg_price'] <=> $a['avg_price']);
        $top = array_slice($comparables, 0, $topN);
        $out = [];
        foreach ($top as $i => $c) {
            $out[] = [
                'anonymous_label' => 'Artista #' . ($i + 1),
                'avg_price' => $c['avg_price'],
                'egi_count' => $c['egi_count'],
                'sales_count_year' => $c['sales_count_year'],
                'profile_completeness' => $c['profile_completeness'],
                'medium' => $c['medium'],
                'career_level' => $c['career_level'],
            ];
        }
        return $out;
    }

    private function percentile(array $values, $target): ?int
    {
        if (empty($values) || $target === null) return null;
        $count = count($values);
        $leq = 0;
        foreach ($values as $v) {
            if ($v <= $target) $leq++;
        }
        return (int) round(($leq / $count) * 100);
    }

    private function median(array $values): ?float
    {
        if (empty($values)) return null;
        sort($values);
        $n = count($values);
        $mid = (int) floor($n / 2);
        if ($n % 2 === 0) {
            return round((((float) $values[$mid - 1]) + ((float) $values[$mid])) / 2.0, 2);
        }
        return round((float) $values[$mid], 2);
    }

    private function deriveCareerLevel(int $completenessScore): string
    {
        if ($completenessScore <= self::CAREER_EMERGING_MAX) return 'emerging';
        if ($completenessScore <= self::CAREER_MID_MAX) return 'mid';
        return 'established';
    }

    private function safeFetchEgis(int $userId): array
    {
        try {
            $r = $this->egiClient->getEgis($userId);
            $data = is_array($r) ? ($r['data'] ?? $r) : [];
            return is_array($data) ? $data : [];
        } catch (\Exception $e) {
            $this->logger->info('EGI getEgis failed in Sestante', ['user_id' => $userId]);
            return [];
        }
    }

    private function safeFetchSales(int $userId): array
    {
        try {
            $r = $this->egiClient->getSalesHistory($userId);
            $data = is_array($r) ? ($r['data'] ?? $r) : [];
            return is_array($data) ? $data : [];
        } catch (\Exception $e) {
            $this->logger->info('EGI getSalesHistory failed in Sestante', ['user_id' => $userId]);
            return [];
        }
    }

    private function emptyPositioning(?string $medium, string $careerLevel, array $ownStats, int $comparablesCount, ?string $error = null): array
    {
        return [
            'has_data' => false,
            'medium_primary' => $medium,
            'career_level' => $careerLevel,
            'comparables_count' => $comparablesCount,
            'own_stats' => $ownStats,
            'metrics' => [
                'percentile_price' => null,
                'percentile_visibility' => null,
                'percentile_portfolio' => null,
                'median_price_comparables' => null,
                'median_sales_comparables' => null,
                'median_egi_count_comparables' => null,
                'price_gap_pct' => null,
            ],
            'top_comparables' => [],
            'error' => $error,
            'analyzed_at' => now()->toIso8601String(),
        ];
    }

    private function emptyOwnStats(): array
    {
        return [
            'egi_count' => 0,
            'avg_price' => null,
            'min_price' => null,
            'max_price' => null,
            'sales_count_year' => 0,
            'profile_completeness' => 0,
            'medium' => null,
        ];
    }

    private function floatOrNull(mixed $v): ?float
    {
        if ($v === null || $v === '') return null;
        return is_numeric($v) ? (float) $v : null;
    }
}
