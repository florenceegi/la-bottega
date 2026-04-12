<?php

/**
 * @package App\Services\Maestro
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Assegna il percorso all'artista al primo accesso basandosi su criteri oggettivi
 */

namespace App\Services\Maestro;

use App\Models\ArtistProfile;
use App\Services\ApiClients\EgiApiClient;

class PercorsoAssignmentService
{
    public function __construct(
        private EgiApiClient $egiClient,
    ) {}

    /**
     * Valuta il profilo e assegna il percorso appropriato.
     *
     * Criteri (da SSOT 04_PERCORSI_CREATOR.md):
     * - ZERO: bio sotto 100 parole, meno di 5 opere, zero vendite, nessuna credenziale
     * - CRESCITA: bio presente, almeno 5 opere, qualche vendita, alcune credenziali
     * - MERCATO: bio completa, portfolio consolidato, vendite regolari, credenziali verificate
     */
    public function assign(ArtistProfile $profile): string
    {
        $userId = $profile->user_id;

        $egis = $this->egiClient->getEgis($userId);
        $biography = $this->egiClient->getBiography($userId);
        $sales = $this->egiClient->getSalesHistory($userId);

        $egisCount = is_array($egis) ? count($egis['data'] ?? $egis) : 0;
        $salesCount = is_array($sales) ? count($sales['data'] ?? $sales) : 0;
        $hasBio = is_array($biography) && !empty($biography['chapters'] ?? $biography['data'] ?? null);

        $percorso = $this->determinePercorso($egisCount, $salesCount, $hasBio, $profile);

        $profile->update([
            'percorso_current' => $percorso,
            'percorso_started_at' => now(),
        ]);

        return $percorso;
    }

    private function determinePercorso(
        int $egisCount,
        int $salesCount,
        bool $hasBio,
        ArtistProfile $profile,
    ): string {
        // MERCATO: profilo maturo
        if (
            $hasBio
            && $egisCount >= 15
            && $salesCount >= 5
            && $profile->credibility_score >= 50
        ) {
            return 'mercato';
        }

        // CRESCITA: profilo intermedio
        if (
            $hasBio
            && $egisCount >= 5
            && $salesCount >= 1
        ) {
            return 'crescita';
        }

        // ZERO: tutti gli altri
        return 'zero';
    }
}
