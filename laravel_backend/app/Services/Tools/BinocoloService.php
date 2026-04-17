<?php

declare(strict_types=1);

/**
 * @package App\Services\Tools
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Binocolo — strumento C.1 La Bottega. Match profilo artista con opportunita
 *          esterne (call for artists, fiere, residenze) da bottega.opportunities.
 *          Scoring: medium match (40) + career level (30) + deadline freshness (20) + country (10).
 */

namespace App\Services\Tools;

use App\Models\ArtistProfile;
use App\Models\Opportunity;
use Illuminate\Support\Carbon;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

class BinocoloService
{
    private const MAX_RESULTS = 10;
    private const CAREER_EMERGING_MAX = 30;
    private const CAREER_MID_MAX = 60;

    private const CAREER_ORDER = ['emerging' => 1, 'mid' => 2, 'established' => 3];

    public function __construct(
        private readonly UltraLogManager $logger,
        private readonly ErrorManagerInterface $errorManager,
    ) {}

    public function match(ArtistProfile $profile, int $limit = self::MAX_RESULTS): array
    {
        try {
            $careerLevel = $this->deriveCareerLevel($profile->profile_completeness_score ?? 0);

            $opportunities = Opportunity::active()
                ->upcoming()
                ->get();

            $scored = [];
            foreach ($opportunities as $opp) {
                $score = $this->scoreOpportunity($opp, $profile, $careerLevel);
                if ($score['total'] <= 0) {
                    continue;
                }
                $scored[] = [
                    'opportunity' => $this->formatOpportunity($opp),
                    'score' => $score['total'],
                    'match_reasons' => $score['reasons'],
                ];
            }

            usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);
            $top = array_slice($scored, 0, $limit);

            $this->logger->info('Binocolo match completed', [
                'user_id' => $profile->user_id,
                'total_opportunities' => count($opportunities),
                'matched' => count($top),
                'career_level' => $careerLevel,
            ]);

            return [
                'career_level' => $careerLevel,
                'medium_primary' => $profile->medium_primary,
                'total_opportunities' => count($opportunities),
                'matched_count' => count($top),
                'results' => $top,
                'analyzed_at' => now()->toIso8601String(),
            ];
        } catch (\Exception $e) {
            $this->errorManager->handle('BOTTEGA_BINOCOLO_ERROR', [
                'user_id' => $profile->user_id,
            ], $e);

            return [
                'career_level' => 'emerging',
                'medium_primary' => null,
                'total_opportunities' => 0,
                'matched_count' => 0,
                'results' => [],
                'error' => __('bottega.binocolo_error'),
                'analyzed_at' => now()->toIso8601String(),
            ];
        }
    }

    private function scoreOpportunity(Opportunity $opp, ArtistProfile $profile, string $careerLevel): array
    {
        $total = 0;
        $reasons = [];

        $mediumScore = $this->scoreMedium($opp, $profile->medium_primary);
        if ($mediumScore['score'] > 0) {
            $total += $mediumScore['score'];
            $reasons[] = $mediumScore['reason'];
        }

        $careerScore = $this->scoreCareer($opp, $careerLevel);
        if ($careerScore['score'] > 0) {
            $total += $careerScore['score'];
            $reasons[] = $careerScore['reason'];
        }

        $deadlineScore = $this->scoreDeadline($opp->deadline);
        if ($deadlineScore['score'] > 0) {
            $total += $deadlineScore['score'];
            $reasons[] = $deadlineScore['reason'];
        }

        $countryScore = $this->scoreCountry($opp->country);
        if ($countryScore['score'] > 0) {
            $total += $countryScore['score'];
            $reasons[] = $countryScore['reason'];
        }

        return ['total' => $total, 'reasons' => $reasons];
    }

    private function scoreMedium(Opportunity $opp, ?string $medium): array
    {
        $accepted = $opp->mediums_accepted;

        if (empty($accepted)) {
            return ['score' => 20, 'reason' => __('bottega.binocolo_reason_medium_any')];
        }

        if ($medium && in_array($medium, $accepted, true)) {
            return ['score' => 40, 'reason' => __('bottega.binocolo_reason_medium_match')];
        }

        return ['score' => 0, 'reason' => ''];
    }

    private function scoreCareer(Opportunity $opp, string $careerLevel): array
    {
        $min = $opp->career_level_min;
        $max = $opp->career_level_max;

        if (!$min && !$max) {
            return ['score' => 15, 'reason' => __('bottega.binocolo_reason_career_open')];
        }

        $artistRank = self::CAREER_ORDER[$careerLevel] ?? 1;
        $minRank = $min ? (self::CAREER_ORDER[$min] ?? 1) : 1;
        $maxRank = $max ? (self::CAREER_ORDER[$max] ?? 3) : 3;

        if ($artistRank >= $minRank && $artistRank <= $maxRank) {
            return ['score' => 30, 'reason' => __('bottega.binocolo_reason_career_match')];
        }

        return ['score' => 0, 'reason' => ''];
    }

    private function scoreDeadline(?Carbon $deadline): array
    {
        if (!$deadline) {
            return ['score' => 10, 'reason' => __('bottega.binocolo_reason_deadline_rolling')];
        }

        $days = now()->diffInDays($deadline, false);

        if ($days < 0) {
            return ['score' => 0, 'reason' => ''];
        }

        if ($days >= 30 && $days <= 180) {
            return ['score' => 20, 'reason' => __('bottega.binocolo_reason_deadline_optimal')];
        }

        if ($days >= 7 && $days < 30) {
            return ['score' => 15, 'reason' => __('bottega.binocolo_reason_deadline_close')];
        }

        if ($days > 180 && $days <= 365) {
            return ['score' => 10, 'reason' => __('bottega.binocolo_reason_deadline_far')];
        }

        return ['score' => 5, 'reason' => __('bottega.binocolo_reason_deadline_ok')];
    }

    private function scoreCountry(?string $country): array
    {
        if (!$country) {
            return ['score' => 10, 'reason' => __('bottega.binocolo_reason_country_international')];
        }

        if ($country === 'IT') {
            return ['score' => 10, 'reason' => __('bottega.binocolo_reason_country_italy')];
        }

        return ['score' => 5, 'reason' => ''];
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

    private function formatOpportunity(Opportunity $opp): array
    {
        return [
            'id' => $opp->id,
            'title' => $opp->title,
            'type' => $opp->type,
            'deadline' => $opp->deadline?->toDateString(),
            'days_remaining' => $opp->deadline ? (int) now()->diffInDays($opp->deadline, false) : null,
            'url' => $opp->url,
            'country' => $opp->country,
            'mediums_accepted' => $opp->mediums_accepted,
            'career_level_min' => $opp->career_level_min,
            'career_level_max' => $opp->career_level_max,
            'description' => $opp->description,
            'source' => $opp->source,
            'verified' => $opp->verified,
        ];
    }
}
