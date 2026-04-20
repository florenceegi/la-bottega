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
    'maestro_temporarily_unavailable' => 'The Maestro is temporarily unavailable. Please try again shortly.',
    'maestro_empty_response' => 'The Maestro did not generate a response. Please try again.',
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

    // ── SEO ───────────────────────────────────────────────────────────
    'meta_description' => 'Objective tools for artists and collectors. Diagnostics, growth paths and informed valuation on FlorenceEGI.',

    // ── Context LLM ───────────────────────────────────────────────────
    'context_percorso' => 'Path',
    'context_completeness' => 'Profile completeness',
    'context_next_step' => 'Next step',
    'no_data_available' => 'No data available',

    // ── BinocoloService ───────────────────────────────────────────
    'binocolo_error' => 'Error during Binocolo analysis',
    'binocolo_reason_medium_match' => 'Medium matches yours',
    'binocolo_reason_medium_any' => 'Open to all mediums',
    'binocolo_reason_career_match' => 'Career level compatible',
    'binocolo_reason_career_open' => 'Open to any career level',
    'binocolo_reason_deadline_optimal' => 'Optimal deadline to prepare your application',
    'binocolo_reason_deadline_close' => 'Deadline close — act now',
    'binocolo_reason_deadline_far' => 'Deadline far — plan calmly',
    'binocolo_reason_deadline_rolling' => 'Rolling applications',
    'binocolo_reason_deadline_ok' => 'Deadline within reach',
    'binocolo_reason_country_italy' => 'Opportunity in Italy',
    'binocolo_reason_country_international' => 'International opportunity',

    // ── MarketPulseService ────────────────────────────────────────
    'market_pulse_error' => 'Error during Market Pulse analysis',
    'market_pulse_no_signals' => 'No market signals available at this time',
    'market_pulse_no_sales' => 'No sales recorded yet — first sale incoming',

    // ── VisibilityTrackerService ──────────────────────────────────
    'visibility_tracker_error' => 'Error during Visibility Tracker analysis',
    'visibility_no_data' => 'No events recorded in this period — start sharing your profile',

    // ── BottegaPriceAdvisorService ────────────────────────────────
    'price_advisor_error' => 'Error during Price Advisor analysis',
    'price_rule_floor' => 'Prices never drop. If the suggestion is lower than current, the current stays.',
    'price_rule_editions' => 'Limited editions: Ed.10 = 30-40% original, Ed.25 = 20-30%, Ed.50 = 15-20%.',
    'price_rule_coherence' => 'Similar artworks (same medium) with price gaps over 50% flagged as incoherent.',

    // ── SestanteService ───────────────────────────────────────────
    'sestante_error' => 'Error during Sestante analysis',
    'sestante_no_comparables' => 'Few comparable artists on FlorenceEGI — at least 3 needed for positioning',
];
