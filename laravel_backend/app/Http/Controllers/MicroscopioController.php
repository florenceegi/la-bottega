<?php

declare(strict_types=1);

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Controller Microscopio — endpoint per eseguire diagnosi e consultare storico.
 */

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use App\Services\Tools\MicroscopioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class MicroscopioController extends Controller
{
    public function __construct(
        private MicroscopioService $microscopioService,
        private ErrorManagerInterface $errorManager,
    ) {}

    /**
     * GET /api/tools/microscopio/run — esegue diagnosi completa.
     */
    public function run(Request $request): JsonResponse
    {
        $profile = ArtistProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json([
                'error' => __('bottega.profile_not_found'),
            ], 404);
        }

        $report = $this->microscopioService->run($profile);

        return response()->json(['data' => $report]);
    }

    /**
     * GET /api/tools/microscopio/history — storico diagnosi.
     */
    public function history(Request $request): JsonResponse
    {
        $profile = ArtistProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json([
                'error' => __('bottega.profile_not_found'),
            ], 404);
        }

        $limit = min((int) $request->query('limit', 10), 50);
        $history = $this->microscopioService->getHistory($profile, $limit);

        return response()->json(['data' => $history]);
    }

    /**
     * POST /api/tools/microscopio/fix/descriptions — rigenera descrizioni via NPE Council.
     */
    public function fixDescriptions(Request $request): JsonResponse
    {
        $profile = ArtistProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json(['error' => __('bottega.profile_not_found')], 404);
        }

        try {
            $language = $request->input('language', app()->getLocale());
            $result = $this->microscopioService->fixDescriptions($profile, $language);

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('BOTTEGA_FIX_DESCRIPTIONS', [
                'user_id' => $request->user()->id,
            ], $e);
        }
    }

    /**
     * POST /api/tools/microscopio/fix/pricing — analisi prezzo via NPE Price Advisor.
     */
    public function fixPricing(Request $request): JsonResponse
    {
        $request->validate(['egi_id' => 'required|integer']);

        $profile = ArtistProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json(['error' => __('bottega.profile_not_found')], 404);
        }

        try {
            $result = $this->microscopioService->fixPricing($request->integer('egi_id'));

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('BOTTEGA_FIX_PRICING', [
                'user_id' => $request->user()->id,
                'egi_id' => $request->integer('egi_id'),
            ], $e);
        }
    }

    /**
     * POST /api/tools/microscopio/fix/coherence — analisi coerenza via NPE CollectionSplitter.
     */
    public function fixCoherence(Request $request): JsonResponse
    {
        $request->validate(['collection_id' => 'required|integer']);

        $profile = ArtistProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json(['error' => __('bottega.profile_not_found')], 404);
        }

        try {
            $result = $this->microscopioService->fixCoherence($request->integer('collection_id'));

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('BOTTEGA_FIX_COHERENCE', [
                'user_id' => $request->user()->id,
                'collection_id' => $request->integer('collection_id'),
            ], $e);
        }
    }
}
