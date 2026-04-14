<?php

declare(strict_types=1);

/**
 * @package App\Services\Tools
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Microscopio — strumento #1 La Bottega. Analisi profonda del profilo artista:
 *          diagnostica base (ProfileDiagnosticService) + verifica coerenza traits tra opere
 *          + segnalazione descrizioni deboli per NPE Council + raccomandazioni azionabili.
 *          Persiste ogni diagnosi per storico e confronto progressi.
 */

namespace App\Services\Tools;

use App\Models\ArtistProfile;
use App\Services\ApiClients\EgiApiClient;
use App\Services\Maestro\ProfileDiagnosticService;
use Illuminate\Support\Facades\DB;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

class MicroscopioService
{
    private const MIN_DESCRIPTION_LENGTH = 80;
    private const TRAIT_COHERENCE_THRESHOLD = 0.6;

    public function __construct(
        private readonly ProfileDiagnosticService $diagnosticService,
        private readonly EgiApiClient $egiClient,
        private readonly UltraLogManager $logger,
        private readonly ErrorManagerInterface $errorManager,
    ) {}

    /**
     * Esegue analisi Microscopio completa e persiste il risultato.
     * Chiamate esterne verificate:
     *   ProfileDiagnosticService::diagnose() — riga 26
     *   EgiApiClient::getEgis() — riga 34
     */
    public function run(ArtistProfile $profile): array
    {
        try {
            $diagnostic = $this->diagnosticService->diagnose($profile);

            $userId = $profile->user_id;
            $egis = $this->egiClient->getEgis($userId);
            $egisData = is_array($egis) ? ($egis['data'] ?? $egis) : [];

            $traitsAnalysis = $this->doTraitsCoherenceAnalysis($egisData);
            $descriptionsAnalysis = $this->doDescriptionsAnalysis($egisData);

            $allFindings = array_merge(
                $diagnostic['findings'],
                $traitsAnalysis['findings'],
                $descriptionsAnalysis['findings']
            );

            $priorityOrder = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];
            usort($allFindings, fn ($a, $b) => ($priorityOrder[$a['priority']] ?? 9) <=> ($priorityOrder[$b['priority']] ?? 9));

            $recommendations = $this->buildRecommendations($allFindings, $profile);

            $report = [
                'total_score' => $diagnostic['total_score'],
                'scores' => $diagnostic['scores'],
                'findings' => $allFindings,
                'findings_count' => count($allFindings),
                'traits_coherence' => $traitsAnalysis['coherence_score'],
                'weak_descriptions_count' => $descriptionsAnalysis['weak_count'],
                'recommendations' => $recommendations,
                'analyzed_at' => now()->toIso8601String(),
            ];

            $this->saveReport($profile, $report);

            $this->logger->info('Microscopio analysis completed', [
                'user_id' => $userId,
                'total_score' => $report['total_score'],
                'findings_count' => $report['findings_count'],
            ]);

            return $report;
        } catch (\Exception $e) {
            $this->errorManager->handle('BOTTEGA_MICROSCOPIO_ERROR', [
                'user_id' => $profile->user_id,
            ], $e);

            return [
                'total_score' => 0,
                'scores' => ['identity' => 0, 'completeness' => 0, 'coherence' => 0, 'visibility' => 0],
                'findings' => [['priority' => 'critical', 'category' => 'system', 'message' => __('bottega.microscopio_error')]],
                'findings_count' => 1,
                'recommendations' => [],
                'analyzed_at' => now()->toIso8601String(),
            ];
        }
    }

    /**
     * Recupera storico diagnosi per l'artista.
     */
    public function getHistory(ArtistProfile $profile, int $limit = 10): array
    {
        $rows = DB::select(
            'SELECT id, total_score, scores, findings_count, analyzed_at
             FROM bottega.microscopio_reports
             WHERE artist_profile_id = :pid
             ORDER BY analyzed_at DESC
             LIMIT :lim',
            ['pid' => $profile->id, 'lim' => $limit]
        );

        return array_map(function ($row) {
            $row = (array) $row;
            $row['scores'] = json_decode($row['scores'], true);
            return $row;
        }, $rows);
    }

    /**
     * Analizza coerenza traits tra opere (B.1.2).
     */
    private function doTraitsCoherenceAnalysis(array $egisData): array
    {
        $findings = [];
        $traitSets = [];

        foreach ($egisData as $egi) {
            $traits = $egi['traits'] ?? [];
            if (empty($traits)) {
                continue;
            }
            $traitNames = array_map(fn ($t) => $t['name'] ?? $t['trait_name'] ?? '', $traits);
            $traitSets[] = array_filter($traitNames);
        }

        if (count($traitSets) < 2) {
            return ['coherence_score' => 1.0, 'findings' => []];
        }

        $allTraits = array_merge(...$traitSets);
        $traitCounts = array_count_values($allTraits);
        $totalSets = count($traitSets);

        $recurringTraits = array_filter($traitCounts, fn ($c) => $c >= ceil($totalSets * self::TRAIT_COHERENCE_THRESHOLD));
        $coherenceScore = count($recurringTraits) > 0
            ? min(1.0, count($recurringTraits) / max(1, count(array_unique($allTraits)) * 0.3))
            : 0.0;

        if ($coherenceScore < 0.4) {
            $findings[] = [
                'priority' => 'high',
                'category' => 'coherence',
                'message' => __('bottega.traits_low_coherence'),
                'action' => 'coherence_check',
            ];
        } elseif ($coherenceScore < 0.7) {
            $findings[] = [
                'priority' => 'medium',
                'category' => 'coherence',
                'message' => __('bottega.traits_moderate_coherence'),
                'action' => 'review_traits',
            ];
        }

        return ['coherence_score' => round($coherenceScore, 2), 'findings' => $findings];
    }

    /**
     * Analizza qualita descrizioni opere (B.1.3).
     */
    private function doDescriptionsAnalysis(array $egisData): array
    {
        $findings = [];
        $weakCount = 0;
        $weakEgis = [];

        foreach ($egisData as $egi) {
            $description = $egi['description'] ?? '';
            $title = $egi['title'] ?? $egi['name'] ?? __('bottega.untitled_artwork');

            if (empty($description) || strlen($description) < self::MIN_DESCRIPTION_LENGTH) {
                $weakCount++;
                $weakEgis[] = $title;
            }
        }

        if ($weakCount > 0 && count($egisData) > 0) {
            $ratio = $weakCount / count($egisData);
            $priority = $ratio > 0.5 ? 'high' : 'medium';

            $findings[] = [
                'priority' => $priority,
                'category' => 'completeness',
                'message' => __('bottega.weak_descriptions') . ' (' . $weakCount . ')',
                'action' => 'npe_council_regenerate',
                'affected_egis' => array_slice($weakEgis, 0, 5),
            ];
        }

        return ['weak_count' => $weakCount, 'findings' => $findings];
    }

    /**
     * Genera raccomandazioni azionabili con deep link EGI.
     */
    private function buildRecommendations(array $findings, ArtistProfile $profile): array
    {
        $recommendations = [];

        foreach (array_slice($findings, 0, 5) as $finding) {
            $rec = [
                'priority' => $finding['priority'],
                'category' => $finding['category'],
                'message' => $finding['message'],
            ];

            $msg = $finding['message'] ?? '';
            $action = $finding['action'] ?? '';

            if ($finding['category'] === 'identity') {
                if (stripos($msg, 'bio') !== false) {
                    $rec['action_url'] = '/profile/biography?from=bottega';
                    $rec['action_label'] = __('bottega.action_complete_bio');
                } elseif (stripos($msg, 'statement') !== false) {
                    $rec['action_url'] = '/profile/edit?from=bottega&section=statement';
                    $rec['action_label'] = __('bottega.action_write_statement');
                }
            } elseif ($finding['category'] === 'completeness') {
                if (stripos($msg, 'opere') !== false || stripos($msg, 'opera') !== false) {
                    $rec['action_url'] = '/egi/create?from=bottega';
                    $rec['action_label'] = __('bottega.action_upload_artwork');
                } elseif (stripos($msg, 'prezzo') !== false) {
                    $rec['action_url'] = '/egi?from=bottega&filter=no_price';
                    $rec['action_label'] = __('bottega.action_set_prices');
                } elseif ($action === 'npe_council_regenerate') {
                    $rec['action_url'] = '/tools/npe-council?from=bottega';
                    $rec['action_label'] = __('bottega.action_improve_descriptions');
                }
            } elseif ($finding['category'] === 'coherence') {
                $rec['action_url'] = '/tools/coherence-check?from=bottega';
                $rec['action_label'] = __('bottega.action_coherence_check');
            }

            $recommendations[] = $rec;
        }

        return $recommendations;
    }

    /**
     * Persiste report diagnosi per storico.
     */
    private function saveReport(ArtistProfile $profile, array $report): void
    {
        DB::insert(
            "INSERT INTO bottega.microscopio_reports
                (artist_profile_id, total_score, scores, findings, findings_count,
                 traits_coherence, weak_descriptions_count, recommendations, analyzed_at)
             VALUES (:pid, :score, :scores, :findings, :fc, :tc, :wdc, :recs, :at)",
            [
                'pid' => $profile->id,
                'score' => $report['total_score'],
                'scores' => json_encode($report['scores']),
                'findings' => json_encode($report['findings']),
                'fc' => $report['findings_count'],
                'tc' => $report['traits_coherence'] ?? null,
                'wdc' => $report['weak_descriptions_count'] ?? 0,
                'recs' => json_encode($report['recommendations']),
                'at' => $report['analyzed_at'],
            ]
        );
    }
}
