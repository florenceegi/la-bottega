<?php

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Endpoint chat Maestro di Bottega — chat, next-step, diagnostica, valutazione ingresso
 */

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use App\Models\CollectorProfile;
use App\Services\Maestro\MaestroDiBottegaService;
use App\Services\Maestro\NextStepEngine;
use App\Services\Maestro\ProfileDiagnosticService;
use App\Services\Maestro\ValutazioneIngressoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaestroController extends Controller
{
    public function __construct(
        private MaestroDiBottegaService $maestroService,
        private NextStepEngine $nextStepEngine,
        private ProfileDiagnosticService $diagnosticService,
        private ValutazioneIngressoService $valutazioneService,
    ) {}

    /**
     * POST /api/maestro/chat
     * Messaggio al Maestro con contesto automatico.
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'session_id' => 'nullable|uuid',
        ]);

        $user = $request->user();
        $instance = $this->resolveInstance($user);

        $result = $this->maestroService->chat(
            $user->id,
            $request->input('message'),
            $instance,
            $request->input('session_id'),
        );

        return response()->json($result);
    }

    /**
     * GET /api/maestro/next-step
     * Prossimo step suggerito per l'artista.
     */
    public function nextStep(Request $request): JsonResponse
    {
        $profile = ArtistProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json(['error' => 'Profilo artista non trovato'], 404);
        }

        $nextStep = $this->nextStepEngine->evaluate($profile);

        return response()->json($nextStep);
    }

    /**
     * GET /api/maestro/profile-diagnostic
     * Diagnostica completa del profilo artista (Microscopio).
     */
    public function profileDiagnostic(Request $request): JsonResponse
    {
        $profile = ArtistProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json(['error' => 'Profilo artista non trovato'], 404);
        }

        $diagnostic = $this->diagnosticService->diagnose($profile);

        return response()->json($diagnostic);
    }

    /**
     * POST /api/maestro/onboarding
     * Valutazione di ingresso al primo accesso.
     */
    public function onboarding(Request $request): JsonResponse
    {
        $user = $request->user();

        $profile = ArtistProfile::firstOrCreate(
            ['user_id' => $user->id],
            []
        );

        if ($profile->onboarding_completed_at) {
            return response()->json([
                'error' => 'Onboarding gia completato',
                'completed_at' => $profile->onboarding_completed_at->toIso8601String(),
            ], 409);
        }

        $result = $this->valutazioneService->execute($profile);

        return response()->json($result);
    }

    /**
     * GET /api/maestro/health
     * Health check — se LLM non risponde in 5s, frontend switch a modalita strumenti diretti (GAP 5).
     */
    public function health(): JsonResponse
    {
        // TODO: verificare connettivita Python AI service
        return response()->json([
            'status' => 'ok',
            'maestro_available' => true,
        ]);
    }

    private function resolveInstance($user): string
    {
        if (CollectorProfile::where('user_id', $user->id)->exists()) {
            return 'collector';
        }

        return 'creator';
    }
}
