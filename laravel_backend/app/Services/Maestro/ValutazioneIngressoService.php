<?php

/**
 * @package App\Services\Maestro
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Genera la valutazione di ingresso personalizzata al primo accesso
 */

namespace App\Services\Maestro;

use App\Models\ArtistProfile;

class ValutazioneIngressoService
{
    public function __construct(
        private ProfileDiagnosticService $diagnosticService,
        private PercorsoAssignmentService $assignmentService,
        private NextStepEngine $nextStepEngine,
    ) {}

    /**
     * Primo accesso: valuta profilo, assegna percorso, genera messaggio di benvenuto.
     * Il messaggio e una "lettura" personalizzata, non un punteggio.
     */
    public function execute(ArtistProfile $profile): array
    {
        // 1. Diagnostica profilo
        $diagnostic = $this->diagnosticService->diagnose($profile);

        // 2. Assegna percorso
        $percorso = $this->assignmentService->assign($profile);

        // 3. Calcola primo step
        $profile->refresh();
        $nextStep = $this->nextStepEngine->evaluate($profile);

        // 4. Segna onboarding completato
        $profile->update(['onboarding_completed_at' => now()]);

        // 5. Costruisci dati per il messaggio di benvenuto
        // Il messaggio vero sara generato dall'LLM con questo contesto
        return [
            'diagnostic' => $diagnostic,
            'percorso_assigned' => $percorso,
            'next_step' => $nextStep,
            'welcome_context' => $this->buildWelcomeContext($diagnostic, $percorso, $nextStep),
        ];
    }

    /**
     * Costruisce il contesto per il messaggio di benvenuto dell'LLM.
     * L'LLM trasformera questo in linguaggio naturale personalizzato.
     */
    private function buildWelcomeContext(array $diagnostic, string $percorso, array $nextStep): array
    {
        $strengths = [];
        $gaps = [];

        foreach ($diagnostic['findings'] as $finding) {
            if ($finding['priority'] === 'critical' || $finding['priority'] === 'high') {
                $gaps[] = $finding['message'];
            }
        }

        if ($diagnostic['scores']['identity'] >= 20) {
            $strengths[] = 'Identita artistica ben definita';
        }
        if ($diagnostic['scores']['completeness'] >= 20) {
            $strengths[] = 'Portfolio ben fornito';
        }
        if ($diagnostic['scores']['visibility'] >= 15) {
            $strengths[] = 'Buona visibilita';
        }

        return [
            'total_score' => $diagnostic['total_score'],
            'strengths' => $strengths,
            'critical_gaps' => array_slice($gaps, 0, 3),
            'percorso' => $percorso,
            'first_step' => $nextStep['description'] ?? null,
        ];
    }
}
