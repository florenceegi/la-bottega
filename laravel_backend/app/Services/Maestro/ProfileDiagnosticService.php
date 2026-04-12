<?php

/**
 * @package App\Services\Maestro
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Diagnostica profilo artista (logica Microscopio) — calcola completezza e lacune
 */

namespace App\Services\Maestro;

use App\Models\ArtistProfile;
use App\Services\ApiClients\EgiApiClient;

class ProfileDiagnosticService
{
    public function __construct(
        private EgiApiClient $egiClient,
    ) {}

    /**
     * Esegue diagnostica completa sul profilo artista via dati EGI.
     * Restituisce score per categoria e lista lacune ordinate per impatto.
     */
    public function diagnose(ArtistProfile $profile): array
    {
        $userId = $profile->user_id;

        $egiProfile = $this->egiClient->getUser($userId);
        $egis = $this->egiClient->getEgis($userId);
        $collections = $this->egiClient->getCollections($userId);
        $biography = $this->egiClient->getBiography($userId);
        $sales = $this->egiClient->getSalesHistory($userId);
        $blockchain = $this->egiClient->getBlockchainStatus($userId);

        $findings = [];
        $scores = [
            'identity' => 0,
            'completeness' => 0,
            'coherence' => 0,
            'visibility' => 0,
        ];

        // --- Identity (25 punti) ---
        $identityScore = 0;

        if (!empty($profile->medium_primary)) {
            $identityScore += 5;
        } else {
            $findings[] = ['priority' => 'high', 'category' => 'identity', 'message' => 'Medium primario non definito'];
        }

        if (!empty($profile->artist_statement_short)) {
            $identityScore += 5;
        } else {
            $findings[] = ['priority' => 'high', 'category' => 'identity', 'message' => 'Artist statement mancante'];
        }

        $hasBio = is_array($biography) && !empty($biography['chapters'] ?? $biography['data'] ?? null);
        if ($hasBio) {
            $identityScore += 10;
        } else {
            $findings[] = ['priority' => 'critical', 'category' => 'identity', 'message' => 'Bio assente — i collezionisti la leggono prima di comprare'];
        }

        if (!empty($profile->instagram_username)) {
            $identityScore += 5;
        } else {
            $findings[] = ['priority' => 'medium', 'category' => 'identity', 'message' => 'Instagram non configurato'];
        }

        $scores['identity'] = min(25, $identityScore);

        // --- Completeness (25 punti) ---
        $completenessScore = 0;
        $egisData = is_array($egis) ? ($egis['data'] ?? $egis) : [];
        $egisCount = count($egisData);

        if ($egisCount >= 5) {
            $completenessScore += 10;
        } elseif ($egisCount > 0) {
            $completenessScore += ($egisCount * 2);
            $findings[] = ['priority' => 'high', 'category' => 'completeness', 'message' => "Solo {$egisCount} opere — servono almeno 5"];
        } else {
            $findings[] = ['priority' => 'critical', 'category' => 'completeness', 'message' => 'Nessuna opera caricata'];
        }

        $collectionsData = is_array($collections) ? ($collections['data'] ?? $collections) : [];
        if (count($collectionsData) > 0) {
            $completenessScore += 5;
        } else {
            $findings[] = ['priority' => 'medium', 'category' => 'completeness', 'message' => 'Nessuna collezione creata'];
        }

        // Prezzi presenti
        $hasPrices = false;
        foreach ($egisData as $egi) {
            if (!empty($egi['price'] ?? null)) {
                $hasPrices = true;
                break;
            }
        }
        if ($hasPrices) {
            $completenessScore += 5;
        } elseif ($egisCount > 0) {
            $findings[] = ['priority' => 'high', 'category' => 'completeness', 'message' => 'Opere senza prezzo — servono per il Price Advisor'];
        }

        // COA Sigillo
        $blockchainData = is_array($blockchain) ? ($blockchain['data'] ?? $blockchain) : [];
        if (!empty($blockchainData)) {
            $completenessScore += 5;
        } elseif ($egisCount > 0) {
            $findings[] = ['priority' => 'medium', 'category' => 'completeness', 'message' => 'Nessun COA Sigillo emesso — argomento di vendita fondamentale'];
        }

        $scores['completeness'] = min(25, $completenessScore);

        // --- Coherence (25 punti) ---
        $coherenceScore = $profile->coherence_score > 0 ? (int) ($profile->coherence_score * 0.25) : 0;
        if ($coherenceScore < 15 && $egisCount >= 5) {
            $findings[] = ['priority' => 'medium', 'category' => 'coherence', 'message' => 'Coerenza stilistica bassa — esegui Coherence Check'];
        }
        $scores['coherence'] = min(25, $coherenceScore);

        // --- Visibility (25 punti) ---
        $visibilityScore = 0;
        $salesData = is_array($sales) ? ($sales['data'] ?? $sales) : [];
        if (count($salesData) > 0) {
            $visibilityScore += 15;
        }
        if (!empty($profile->instagram_username) && $profile->instagram_weeks_active >= 4) {
            $visibilityScore += 10;
        }
        $scores['visibility'] = min(25, $visibilityScore);

        // Calcola score totale
        $totalScore = array_sum($scores);

        // Aggiorna score sul profilo
        $profile->update(['profile_completeness_score' => $totalScore]);

        // Ordina findings per priorita
        $priorityOrder = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];
        usort($findings, fn($a, $b) => ($priorityOrder[$a['priority']] ?? 9) <=> ($priorityOrder[$b['priority']] ?? 9));

        return [
            'total_score' => $totalScore,
            'scores' => $scores,
            'findings' => $findings,
            'findings_count' => count($findings),
        ];
    }
}
