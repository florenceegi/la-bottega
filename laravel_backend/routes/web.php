<?php

/**
 * @package App\Routes
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Web routes — SPA React serve via Blade layout
 */

use Illuminate\Support\Facades\Route;

Route::get('/{any?}', function () {
    return view('layouts.app');
})->where('any', '.*');
