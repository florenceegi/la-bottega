<?php

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.1.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-14
 * @purpose Endpoint percorso artista — stato, completamento step, storico
 */

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use App\Models\StepCompletion;
use App\Services\Maestro\NextStepEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class PercorsoController extends Controller
{
    public function __construct(
        private NextStepEngine $nextStepEngine,
        private ErrorManagerInterface $errorManager,
    ) {}

    /**
     * GET /api/percorso/status
     */
    public function status(Request $request): JsonResponse
    {
        $profile = ArtistProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json(['error' => __('bottega.profile_not_found')], 404);
        }

        try {
            $status = $this->nextStepEngine->getStatus($profile);
            return response()->json($status);
        } catch (\Exception $e) {
            return $this->errorManager->handle('BOTTEGA_PERCORSO_STATUS_ERROR', [
                'user_id' => $request->user()->id,
            ], $e);
        }
    }

    /**
     * POST /api/percorso/complete-step
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

        try {
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
        } catch (\Exception $e) {
            return $this->errorManager->handle('BOTTEGA_PERCORSO_COMPLETE_ERROR', [
                'user_id' => $request->user()->id,
                'step_number' => $request->input('step_number'),
            ], $e);
        }
    }

    /**
     * GET /api/percorso/history
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
