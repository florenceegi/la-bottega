<?php

/**
 * @package App\Services\Maestro
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Determina il prossimo step per l'artista — gerarchia fissa non negoziabile
 */

namespace App\Services\Maestro;

use App\Models\ArtistProfile;
use App\Models\StepCompletion;
use App\Services\ApiClients\EgiApiClient;

class NextStepEngine
{
    /**
     * Gerarchia fissa: Completezza > Coerenza > Visibilita > Crescita.
     * L'artista non vede questa gerarchia. Vede solo il passo successivo.
     */
    private const HIERARCHY = ['completeness', 'coherence', 'visibility', 'growth'];

    /**
     * Mappa fase → step nel Percorso ZERO (16 step totali, 4 per fase).
     */
    private const PERCORSO_ZERO_STEPS = [
        1 => ['fase' => 1, 'label' => 'Scegli il tuo medium primario', 'field_check' => 'medium_primary'],
        2 => ['fase' => 1, 'label' => 'Scrivi la tua Origine', 'field_check' => null],
        3 => ['fase' => 1, 'label' => 'Definisci il tuo stile in una frase', 'field_check' => 'artist_statement_short'],
        4 => ['fase' => 1, 'label' => 'Carica le tue 5 opere fondanti', 'field_check' => null],
        5 => ['fase' => 2, 'label' => 'Definisci la tua logica di prezzo', 'field_check' => null],
        6 => ['fase' => 2, 'label' => 'Ottimizza il profilo pubblico FlorenceEGI', 'field_check' => null],
        7 => ['fase' => 2, 'label' => 'Imposta Instagram come canale di scoperta', 'field_check' => 'instagram_username'],
        8 => ['fase' => 2, 'label' => 'Prima storia Instagram con link FlorenceEGI', 'field_check' => null],
        9 => ['fase' => 3, 'label' => 'Costruisci la prima lista email — i 50 contatti zero', 'field_check' => null],
        10 => ['fase' => 3, 'label' => 'Identifica i 10 potenziali primi collezionisti', 'field_check' => null],
        11 => ['fase' => 3, 'label' => 'Il messaggio diretto', 'field_check' => null],
        12 => ['fase' => 3, 'label' => 'Prima vendita — documentarla e celebrarla', 'field_check' => null],
        13 => ['fase' => 4, 'label' => 'Prima newsletter mensile', 'field_check' => null],
        14 => ['fase' => 4, 'label' => 'Identifica primo interior designer o spazio commerciale', 'field_check' => null],
        15 => ['fase' => 4, 'label' => 'Seconda collezione con coerenza verificata', 'field_check' => null],
        16 => ['fase' => 4, 'label' => 'Prima credenziale EGI Credential', 'field_check' => null],
    ];

    private const FASE_LABELS = [
        1 => 'Identita',
        2 => 'Presenza Digitale',
        3 => 'Prima Vendita',
        4 => 'Costruire il Ritmo',
    ];

    public function __construct(
        private EgiApiClient $egiClient,
    ) {}

    /**
     * Valuta il profilo artista e restituisce il prossimo step da proporre.
     * Restituisce sempre UN SOLO step — mai anticipare, mai mostrare tutto.
     */
    public function evaluate(ArtistProfile $profile): array
    {
        $percorso = $profile->percorso_current ?? 'zero';
        $completions = StepCompletion::where('artist_profile_id', $profile->id)
            ->where('percorso', $percorso)
            ->whereIn('status', ['completed', 'skipped'])
            ->pluck('step_number')
            ->toArray();

        $steps = $this->getStepsForPercorso($percorso);

        foreach ($steps as $stepNumber => $stepDef) {
            if (in_array($stepNumber, $completions)) {
                continue;
            }

            // Verifica automatica per step con field_check
            if ($stepDef['field_check'] && !empty($profile->{$stepDef['field_check']})) {
                continue;
            }

            return [
                'percorso' => $percorso,
                'fase' => $stepDef['fase'],
                'fase_label' => self::FASE_LABELS[$stepDef['fase']] ?? '',
                'step_number' => $stepNumber,
                'description' => $stepDef['label'],
                'total_completed' => count($completions),
                'total_steps' => count($steps),
            ];
        }

        return [
            'percorso' => $percorso,
            'fase' => 4,
            'fase_label' => 'Completato',
            'step_number' => null,
            'description' => 'Percorso completato — profilo eccellente',
            'total_completed' => count($completions),
            'total_steps' => count($steps),
        ];
    }

    /**
     * Restituisce lo stato corrente del percorso: fase, step completati, progresso.
     */
    public function getStatus(ArtistProfile $profile): array
    {
        $nextStep = $this->evaluate($profile);
        $percorso = $profile->percorso_current ?? 'zero';

        $completionsByFase = StepCompletion::where('artist_profile_id', $profile->id)
            ->where('percorso', $percorso)
            ->where('status', 'completed')
            ->get()
            ->groupBy('fase')
            ->map->count()
            ->toArray();

        return [
            'percorso' => $percorso,
            'current_fase' => $nextStep['fase'],
            'current_fase_label' => $nextStep['fase_label'],
            'next_step' => $nextStep,
            'completions_by_fase' => $completionsByFase,
            'started_at' => $profile->percorso_started_at?->toIso8601String(),
        ];
    }

    private function getStepsForPercorso(string $percorso): array
    {
        // Per ora solo Percorso ZERO implementato — CRESCITA e MERCATO in fasi successive
        return match ($percorso) {
            'zero' => self::PERCORSO_ZERO_STEPS,
            default => self::PERCORSO_ZERO_STEPS,
        };
    }
}
