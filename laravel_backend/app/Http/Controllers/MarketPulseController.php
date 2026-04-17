<?php

declare(strict_types=1);

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Controller Market Pulse — segnali di mercato + sintesi vendite artista.
 */

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use App\Services\Tools\MarketPulseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class MarketPulseController extends Controller
{
    public function __construct(
        private MarketPulseService $marketPulseService,
        private ErrorManagerInterface $errorManager,
    ) {}

    /**
     * GET /api/tools/market-pulse/pulse — segnali di mercato + vendite artista.
     */
    public function pulse(Request $request): JsonResponse
    {
        $profile = ArtistProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json([
                'error' => __('bottega.profile_not_found'),
            ], 404);
        }

        try {
            $result = $this->marketPulseService->pulse($profile);

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('BOTTEGA_MARKET_PULSE', [
                'user_id' => $request->user()->id,
            ], $e);
        }
    }
}
