<?php

/**
 * @package Lang\En
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-14
 * @purpose English translations La Bottega — SSOT backend i18n
 */

return [

    // ── Controller errors ──────────────────────────────────────────
    'profile_not_found' => 'Artist profile not found',
    'onboarding_already_completed' => 'Onboarding already completed',

    // ── ProfileDiagnosticService — findings ────────────────────────
    'diag_medium_missing' => 'Primary medium not defined',
    'diag_statement_missing' => 'Artist statement missing',
    'diag_bio_absent' => 'Bio missing — collectors read it before buying',
    'diag_instagram_missing' => 'Instagram not configured',
    'diag_few_artworks' => 'Not enough artworks — at least 5 required',
    'diag_no_artworks' => 'No artworks uploaded',
    'diag_no_collections' => 'No collections created',
    'diag_no_prices' => 'Artworks without prices — needed for Price Advisor',
    'diag_no_coa' => 'No Sigillo COA issued — a key selling point',
    'diag_low_coherence' => 'Low stylistic coherence — run Coherence Check',

    // ── MaestroDiBottegaService ────────────────────────────────────
    'maestro_not_connected' => '[Maestro not yet connected to Python AI service]',
    'no_data_available' => 'No data available.',
    'context_artworks' => 'Artworks uploaded',
    'context_collections' => 'Collections',
    'context_sales' => 'Sales',
    'context_certifications' => 'Blockchain certifications',

    // ── ValutazioneIngressoService — strengths ─────────────────────
    'strength_identity' => 'Well-defined artistic identity',
    'strength_portfolio' => 'Well-stocked portfolio',
    'strength_visibility' => 'Good visibility',

    // ── NextStepEngine — step labels (Percorso ZERO) ───────────────
    'step_1' => 'Choose your primary medium',
    'step_2' => 'Write your Origin story',
    'step_3' => 'Define your style in one sentence',
    'step_4' => 'Upload your 5 founding artworks',
    'step_5' => 'Define your pricing logic',
    'step_6' => 'Optimise your FlorenceEGI public profile',
    'step_7' => 'Set up Instagram as a discovery channel',
    'step_8' => 'First Instagram story with FlorenceEGI link',
    'step_9' => 'Build your first email list — the initial 50 contacts',
    'step_10' => 'Identify your 10 potential first collectors',
    'step_11' => 'The direct message',
    'step_12' => 'First sale — document it and celebrate',
    'step_13' => 'First monthly newsletter',
    'step_14' => 'Identify first interior designer or commercial space',
    'step_15' => 'Second collection with verified coherence',
    'step_16' => 'First EGI Credential',

    // ── NextStepEngine — fase labels ───────────────────────────────
    'fase_1' => 'Identity',
    'fase_2' => 'Digital Presence',
    'fase_3' => 'First Sale',
    'fase_4' => 'Building the Rhythm',
    'fase_completed' => 'Completed',
    'percorso_completed' => 'Path completed — excellent profile',

    // ── MicroscopioService — findings & actions ────────────────────
    'microscopio_error' => 'Error during Microscopio analysis',
    'traits_low_coherence' => 'Very low trait coherence across artworks — fragmented visual identity',
    'traits_moderate_coherence' => 'Moderate trait coherence — some recurring elements, but more cohesion needed',
    'weak_descriptions' => 'Artwork descriptions missing or too short',
    'untitled_artwork' => 'Untitled artwork',

    // ── MicroscopioService — action labels ─────────────────────────
    'action_complete_bio' => 'Complete your bio',
    'action_write_statement' => 'Write your artist statement',
    'action_upload_artwork' => 'Upload an artwork',
    'action_set_prices' => 'Set your prices',
    'action_improve_descriptions' => 'Improve descriptions',
    'action_coherence_check' => 'Run Coherence Check',

    // ── MicroscopioService — fix NPE results ──────────────────────
    'fix_no_weak_descriptions' => 'All descriptions are already adequate',
    'fix_descriptions_sent' => 'Descriptions sent to NPE Council for regeneration',
    'fix_descriptions_error' => 'Error during description regeneration',
    'fix_pricing_complete' => 'Price analysis completed',
    'fix_pricing_error' => 'Error during price analysis',
    'fix_coherence_low' => 'Low coherence — reorganization suggestion generated',
    'fix_coherence_ok' => 'Collection is coherent — no action needed',
    'fix_coherence_error' => 'Error during coherence analysis',
];
