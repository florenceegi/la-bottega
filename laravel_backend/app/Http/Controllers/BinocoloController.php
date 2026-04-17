<?php

declare(strict_types=1);

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Controller Binocolo — endpoint match profilo artista con opportunita esterne.
 */

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use App\Services\Tools\BinocoloService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class BinocoloController extends Controller
{
    public function __construct(
        private BinocoloService $binocoloService,
        private ErrorManagerInterface $errorManager,
    ) {}

    /**
     * GET /api/tools/binocolo/match — opportunita matching con profilo.
     */
    public function match(Request $request): JsonResponse
    {
        $profile = ArtistProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json([
                'error' => __('bottega.profile_not_found'),
            ], 404);
        }

        try {
            $limit = min((int) $request->query('limit', 10), 50);
            $result = $this->binocoloService->match($profile, $limit);

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('BOTTEGA_BINOCOLO_MATCH', [
                'user_id' => $request->user()->id,
            ], $e);
        }
    }
}
