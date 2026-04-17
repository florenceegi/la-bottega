<?php

declare(strict_types=1);

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Controller Visibility Tracker — funnel analytics artista con comparazione periodo.
 */

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use App\Services\Tools\VisibilityTrackerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

class VisibilityTrackerController extends Controller
{
    public function __construct(
        private VisibilityTrackerService $visibilityService,
        private ErrorManagerInterface $errorManager,
    ) {}

    /**
     * GET /api/tools/visibility/report — funnel + breakdown eventi ultimi N giorni.
     */
    public function report(Request $request): JsonResponse
    {
        $profile = ArtistProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json([
                'error' => __('bottega.profile_not_found'),
            ], 404);
        }

        try {
            $days = max(1, min((int) $request->query('days', 7), 90));
            $result = $this->visibilityService->report($profile, $days);

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('BOTTEGA_VISIBILITY_REPORT', [
                'user_id' => $request->user()->id,
            ], $e);
        }
    }
}
