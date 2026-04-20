<?php

declare(strict_types=1);

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-20
 * @purpose Controller Sestante — positioning artista vs comparabili su FlorenceEGI.
 */

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use App\Services\Tools\SestanteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class SestanteController extends Controller
{
    public function __construct(
        private SestanteService $sestanteService,
        private ErrorManagerInterface $errorManager,
    ) {}

    /**
     * GET /api/tools/sestante/position — percentile + gap vs comparabili.
     */
    public function position(Request $request): JsonResponse
    {
        $profile = ArtistProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json([
                'error' => __('bottega.profile_not_found'),
            ], 404);
        }

        try {
            $result = $this->sestanteService->position($profile);
            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('BOTTEGA_SESTANTE', [
                'user_id' => $request->user()->id,
            ], $e);
        }
    }
}
