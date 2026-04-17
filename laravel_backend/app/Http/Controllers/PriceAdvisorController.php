<?php

declare(strict_types=1);

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Controller Price Advisor — wrapper NPE con contesto Bottega.
 */

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use App\Services\Tools\BottegaPriceAdvisorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class PriceAdvisorController extends Controller
{
    public function __construct(
        private BottegaPriceAdvisorService $priceAdvisor,
        private ErrorManagerInterface $errorManager,
    ) {}

    /**
     * GET /api/tools/price-advisor/analyze — portfolio completo.
     */
    public function analyze(Request $request): JsonResponse
    {
        $profile = ArtistProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json(['error' => __('bottega.profile_not_found')], 404);
        }

        try {
            $result = $this->priceAdvisor->analyze($profile);
            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('BOTTEGA_PRICE_ADVISOR_ANALYZE', [
                'user_id' => $request->user()->id,
            ], $e);
        }
    }

    /**
     * GET /api/tools/price-advisor/egi/{egiId} — focus singola opera.
     */
    public function analyzeEgi(Request $request, int $egiId): JsonResponse
    {
        $profile = ArtistProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json(['error' => __('bottega.profile_not_found')], 404);
        }

        try {
            $result = $this->priceAdvisor->analyze($profile, $egiId);
            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('BOTTEGA_PRICE_ADVISOR_EGI', [
                'user_id' => $request->user()->id,
                'egi_id' => $egiId,
            ], $e);
        }
    }
}
