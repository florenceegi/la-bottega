<?php

/**
 * @package App\Routes
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose API routes La Bottega — Sanctum protected
 */

use App\Http\Controllers\BinocoloController;
use App\Http\Controllers\MaestroController;
use App\Http\Controllers\MarketPulseController;
use App\Http\Controllers\MicroscopioController;
use App\Http\Controllers\PercorsoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

// Health check readiness (deep) — no auth, no middleware (ALB + CI/CD gate)
// Pattern B3: /health = nginx stub liveness, /api/health = Laravel readiness deep check
Route::get('/health', \App\Http\Controllers\HealthController::class)->name('api.health');

Route::get('/maestro/health', [MaestroController::class, 'health']);

/*
|--------------------------------------------------------------------------
| Authenticated routes (Sanctum SSO)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // --- Maestro di Bottega ---
    Route::post('/maestro/chat', [MaestroController::class, 'chat']);
    Route::get('/maestro/next-step', [MaestroController::class, 'nextStep']);
    Route::get('/maestro/profile-diagnostic', [MaestroController::class, 'profileDiagnostic']);
    Route::post('/maestro/onboarding', [MaestroController::class, 'onboarding']);

    // --- Strumenti ---
    Route::get('/tools/microscopio/run', [MicroscopioController::class, 'run']);
    Route::get('/tools/microscopio/history', [MicroscopioController::class, 'history']);
    Route::post('/tools/microscopio/fix/descriptions', [MicroscopioController::class, 'fixDescriptions']);
    Route::post('/tools/microscopio/fix/pricing', [MicroscopioController::class, 'fixPricing']);
    Route::post('/tools/microscopio/fix/coherence', [MicroscopioController::class, 'fixCoherence']);

    Route::get('/tools/binocolo/match', [BinocoloController::class, 'match']);

    Route::get('/tools/market-pulse/pulse', [MarketPulseController::class, 'pulse']);

    // --- Percorso ---
    Route::get('/percorso/status', [PercorsoController::class, 'status']);
    Route::post('/percorso/complete-step', [PercorsoController::class, 'completeStep']);
    Route::get('/percorso/history', [PercorsoController::class, 'history']);
});
