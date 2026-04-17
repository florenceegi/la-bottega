<?php

declare(strict_types=1);

/**
 * @package App\Services\Tools
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Bottega Price Advisor — wrappa NPE Pricing Advisor con contesto Bottega.
 *          Regole non negoziabili: prezzi non scendono mai. Range edizioni limitate.
 *          Alert su incoerenze tra opere simili. Struttura funzionante con zero dati.
 */

namespace App\Services\Tools;

use App\Models\ArtistProfile;
use App\Services\ApiClients\EgiApiClient;
use App\Services\ApiClients\NpeApiClient;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

class BottegaPriceAdvisorService
{
    private const EDITION_RANGES = [
        10 => ['min' => 0.30, 'max' => 0.40],
        25 => ['min' => 0.20, 'max' => 0.30],
        50 => ['min' => 0.15, 'max' => 0.20],
    ];

    private const INCOHERENCE_THRESHOLD = 0.50;

    public function __construct(
        private readonly EgiApiClient $egiClient,
        private readonly NpeApiClient $npeClient,
        private readonly UltraLogManager $logger,
        private readonly ErrorManagerInterface $errorManager,
    ) {}

    public function analyze(ArtistProfile $profile, ?int $egiIdFocus = null): array
    {
        try {
            $userId = $profile->user_id;
            $egis = $this->fetchArtistEgis($userId);

            if (empty($egis)) {
                return $this->emptyAnalysis();
            }

            $targets = $egiIdFocus
                ? array_filter($egis, fn ($e) => (int) ($e['id'] ?? 0) === $egiIdFocus)
                : $egis;

            $analyses = [];
            foreach ($targets as $egi) {
                $analysis = $this->analyzeSingle($egi);
                if ($analysis !== null) {
                    $analyses[] = $analysis;
                }
            }

            $incoherences = $this->detectIncoherences($analyses);
            $editionSuggestions = $this->deriveEditionSuggestions($analyses);

            $this->logger->info('Bottega Price Advisor analyzed', [
                'user_id' => $userId,
                'analyzed_count' => count($analyses),
                'incoherences_count' => count($incoherences),
            ]);

            return [
                'has_data' => !empty($analyses),
                'analyzed_count' => count($analyses),
                'items' => $analyses,
                'incoherences' => $incoherences,
                'edition_suggestions' => $editionSuggestions,
                'rules' => $this->rulesSummary(),
                'analyzed_at' => now()->toIso8601String(),
            ];
        } catch (\Exception $e) {
            $this->errorManager->handle('BOTTEGA_PRICE_ADVISOR_ERROR', [
                'user_id' => $profile->user_id,
            ], $e);

            return $this->emptyAnalysis(__('bottega.price_advisor_error'));
        }
    }

    private function fetchArtistEgis(int $userId): array
    {
        try {
            $response = $this->egiClient->getEgis($userId);
            $egis = is_array($response) ? ($response['data'] ?? $response) : [];
            return is_array($egis) ? $egis : [];
        } catch (\Exception $e) {
            $this->logger->warning('EGI getEgis failed, returning empty', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    private function analyzeSingle(array $egi): ?array
    {
        $egiId = (int) ($egi['id'] ?? 0);
        if ($egiId === 0) {
            return null;
        }

        $currentPrice = $this->floatOrNull($egi['price'] ?? $egi['price_eur'] ?? null);

        $npeResult = null;
        try {
            $npeResult = $this->npeClient->getPriceAdvisorResult($egiId);
        } catch (\Exception $e) {
            $this->logger->info('NPE price advisor unavailable for EGI', [
                'egi_id' => $egiId,
                'error' => $e->getMessage(),
            ]);
        }

        $suggestedPrice = $this->floatOrNull(
            $npeResult['suggested_price']
                ?? $npeResult['price']
                ?? $npeResult['recommended']
                ?? null
        );
        $rangeMin = $this->floatOrNull($npeResult['min_price'] ?? $npeResult['range_min'] ?? null);
        $rangeMax = $this->floatOrNull($npeResult['max_price'] ?? $npeResult['range_max'] ?? null);

        $wouldLower = $currentPrice !== null
            && $suggestedPrice !== null
            && $suggestedPrice < $currentPrice;

        $finalSuggested = $wouldLower ? $currentPrice : $suggestedPrice;

        return [
            'egi_id' => $egiId,
            'title' => $egi['title'] ?? null,
            'medium' => $egi['medium'] ?? null,
            'dimension_cm' => $egi['dimension_cm'] ?? $egi['size'] ?? null,
            'current_price' => $currentPrice,
            'suggested_price' => $finalSuggested,
            'suggested_range' => [
                'min' => $rangeMin,
                'max' => $rangeMax,
            ],
            'npe_confidence' => $npeResult['confidence'] ?? null,
            'rule_floor_applied' => $wouldLower,
            'has_npe_data' => $npeResult !== null,
        ];
    }

    private function detectIncoherences(array $analyses): array
    {
        $grouped = [];
        foreach ($analyses as $item) {
            $price = $item['current_price'];
            $medium = $item['medium'] ?? null;
            if ($price === null || $medium === null) {
                continue;
            }
            $key = $medium;
            $grouped[$key] ??= [];
            $grouped[$key][] = $item;
        }

        $incoherences = [];
        foreach ($grouped as $medium => $items) {
            if (count($items) < 2) {
                continue;
            }
            $prices = array_column($items, 'current_price');
            $min = min($prices);
            $max = max($prices);
            if ($min <= 0) {
                continue;
            }
            $gap = ($max - $min) / $min;
            if ($gap >= self::INCOHERENCE_THRESHOLD) {
                $incoherences[] = [
                    'medium' => $medium,
                    'min_price' => $min,
                    'max_price' => $max,
                    'gap_pct' => round($gap * 100, 1),
                    'items_count' => count($items),
                    'egi_ids' => array_column($items, 'egi_id'),
                ];
            }
        }

        return $incoherences;
    }

    private function deriveEditionSuggestions(array $analyses): array
    {
        $suggestions = [];
        foreach ($analyses as $item) {
            $original = $item['suggested_price'] ?? $item['current_price'] ?? null;
            if ($original === null || $original <= 0) {
                continue;
            }
            $ranges = [];
            foreach (self::EDITION_RANGES as $edSize => $pct) {
                $ranges[] = [
                    'edition_size' => $edSize,
                    'min_price' => round($original * $pct['min'], 2),
                    'max_price' => round($original * $pct['max'], 2),
                ];
            }
            $suggestions[] = [
                'egi_id' => $item['egi_id'],
                'title' => $item['title'],
                'base_price' => $original,
                'ranges' => $ranges,
            ];
        }
        return $suggestions;
    }

    private function rulesSummary(): array
    {
        return [
            'price_floor' => __('bottega.price_rule_floor'),
            'edition_ranges' => __('bottega.price_rule_editions'),
            'coherence' => __('bottega.price_rule_coherence'),
        ];
    }

    private function emptyAnalysis(?string $error = null): array
    {
        return [
            'has_data' => false,
            'analyzed_count' => 0,
            'items' => [],
            'incoherences' => [],
            'edition_suggestions' => [],
            'rules' => $this->rulesSummary(),
            'error' => $error,
            'analyzed_at' => now()->toIso8601String(),
        ];
    }

    private function floatOrNull(mixed $v): ?float
    {
        if ($v === null || $v === '') {
            return null;
        }
        return is_numeric($v) ? (float) $v : null;
    }
}
