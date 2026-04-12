<?php

/**
 * @package App\Routes
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose API routes La Bottega — Sanctum protected
 */

use App\Http\Controllers\MaestroController;
use App\Http\Controllers\PercorsoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
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

    // --- Percorso ---
    Route::get('/percorso/status', [PercorsoController::class, 'status']);
    Route::post('/percorso/complete-step', [PercorsoController::class, 'completeStep']);
    Route::get('/percorso/history', [PercorsoController::class, 'history']);
});
