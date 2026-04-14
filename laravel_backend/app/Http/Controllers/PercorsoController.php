<?php

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Endpoint percorso artista — stato, completamento step, storico
 */

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use App\Models\StepCompletion;
use App\Services\Maestro\NextStepEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PercorsoController extends Controller
{
    public function __construct(
        private NextStepEngine $nextStepEngine,
    ) {}

    /**
     * GET /api/percorso/status
     * Stato corrente del percorso: fase, step completati, progresso.
     */
    public function status(Request $request): JsonResponse
    {
        $profile = ArtistProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json(['error' => __('bottega.profile_not_found')], 404);
        }

        $status = $this->nextStepEngine->getStatus($profile);

        return response()->json($status);
    }

    /**
     * POST /api/percorso/complete-step
     * Segna uno step come completato.
     */
    public function completeStep(Request $request): JsonResponse
    {
        $request->validate([
            'step_number' => 'required|integer|min:1|max:16',
            'verification_data' => 'nullable|array',
        ]);

        $profile = ArtistProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json(['error' => __('bottega.profile_not_found')], 404);
        }

        $percorso = $profile->percorso_current ?? 'zero';
        $stepNumber = $request->input('step_number');
        $fase = (int) ceil($stepNumber / 4);

        $completion = StepCompletion::updateOrCreate(
            [
                'artist_profile_id' => $profile->id,
                'percorso' => $percorso,
                'fase' => $fase,
                'step_number' => $stepNumber,
            ],
            [
                'status' => 'completed',
                'completed_at' => now(),
                'verification_data' => $request->input('verification_data'),
            ],
        );

        $nextStep = $this->nextStepEngine->evaluate($profile);

        return response()->json([
            'completed' => $completion->toArray(),
            'next_step' => $nextStep,
        ]);
    }

    /**
     * GET /api/percorso/history
     * Storico completamenti step.
     */
    public function history(Request $request): JsonResponse
    {
        $profile = ArtistProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json(['error' => __('bottega.profile_not_found')], 404);
        }

        $completions = StepCompletion::where('artist_profile_id', $profile->id)
            ->orderBy('step_number')
            ->get();

        return response()->json([
            'percorso' => $profile->percorso_current,
            'completions' => $completions,
        ]);
    }
}
