<?php

/**
 * @package Lang\It
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-14
 * @purpose Traduzioni italiane La Bottega — SSOT backend i18n
 */

return [

    // ── Controller errors ──────────────────────────────────────────
    'profile_not_found' => 'Profilo artista non trovato',
    'onboarding_already_completed' => 'Onboarding già completato',

    // ── ProfileDiagnosticService — findings ────────────────────────
    'diag_medium_missing' => 'Medium primario non definito',
    'diag_statement_missing' => 'Artist statement mancante',
    'diag_bio_absent' => 'Bio assente — i collezionisti la leggono prima di comprare',
    'diag_instagram_missing' => 'Instagram non configurato',
    'diag_few_artworks' => 'Opere insufficienti — servono almeno 5',
    'diag_no_artworks' => 'Nessuna opera caricata',
    'diag_no_collections' => 'Nessuna collezione creata',
    'diag_no_prices' => 'Opere senza prezzo — servono per il Price Advisor',
    'diag_no_coa' => 'Nessun COA Sigillo emesso — argomento di vendita fondamentale',
    'diag_low_coherence' => 'Coerenza stilistica bassa — esegui Coherence Check',

    // ── MaestroDiBottegaService ────────────────────────────────────
    'maestro_not_connected' => '[Maestro non ancora connesso al Python AI service]',
    'no_data_available' => 'Nessun dato disponibile.',
    'context_artworks' => 'Opere caricate',
    'context_collections' => 'Collezioni',
    'context_sales' => 'Vendite',
    'context_certifications' => 'Certificazioni blockchain',

    // ── ValutazioneIngressoService — strengths ─────────────────────
    'strength_identity' => 'Identità artistica ben definita',
    'strength_portfolio' => 'Portfolio ben fornito',
    'strength_visibility' => 'Buona visibilità',

    // ── NextStepEngine — step labels (Percorso ZERO) ───────────────
    'step_1' => 'Scegli il tuo medium primario',
    'step_2' => 'Scrivi la tua Origine',
    'step_3' => 'Definisci il tuo stile in una frase',
    'step_4' => 'Carica le tue 5 opere fondanti',
    'step_5' => 'Definisci la tua logica di prezzo',
    'step_6' => 'Ottimizza il profilo pubblico FlorenceEGI',
    'step_7' => 'Imposta Instagram come canale di scoperta',
    'step_8' => 'Prima storia Instagram con link FlorenceEGI',
    'step_9' => 'Costruisci la prima lista email — i 50 contatti zero',
    'step_10' => 'Identifica i 10 potenziali primi collezionisti',
    'step_11' => 'Il messaggio diretto',
    'step_12' => 'Prima vendita — documentarla e celebrarla',
    'step_13' => 'Prima newsletter mensile',
    'step_14' => 'Identifica primo interior designer o spazio commerciale',
    'step_15' => 'Seconda collezione con coerenza verificata',
    'step_16' => 'Prima credenziale EGI Credential',

    // ── NextStepEngine — fase labels ───────────────────────────────
    'fase_1' => 'Identità',
    'fase_2' => 'Presenza Digitale',
    'fase_3' => 'Prima Vendita',
    'fase_4' => 'Costruire il Ritmo',
    'fase_completed' => 'Completato',
    'percorso_completed' => 'Percorso completato — profilo eccellente',

    // ── MicroscopioService — findings & actions ────────────────────
    'microscopio_error' => 'Errore durante l\'analisi Microscopio',
    'traits_low_coherence' => 'Coerenza traits molto bassa tra le opere — identità visiva frammentata',
    'traits_moderate_coherence' => 'Coerenza traits moderata — alcuni elementi ricorrenti, ma serve più coesione',
    'weak_descriptions' => 'Descrizioni opere assenti o troppo brevi',
    'untitled_artwork' => 'Opera senza titolo',

    // ── MicroscopioService — action labels ─────────────────────────
    'action_complete_bio' => 'Completa la tua bio',
    'action_write_statement' => 'Scrivi il tuo artist statement',
    'action_upload_artwork' => 'Carica un\'opera',
    'action_set_prices' => 'Imposta i prezzi',
    'action_improve_descriptions' => 'Migliora le descrizioni',
    'action_coherence_check' => 'Esegui Coherence Check',

    // ── MicroscopioService — fix NPE results ──────────────────────
    'fix_no_weak_descriptions' => 'Tutte le descrizioni sono già adeguate',
    'fix_descriptions_sent' => 'Descrizioni inviate al Council NPE per rigenerazione',
    'fix_descriptions_error' => 'Errore durante la rigenerazione delle descrizioni',
    'fix_pricing_complete' => 'Analisi prezzi completata',
    'fix_pricing_error' => 'Errore durante l\'analisi prezzi',
    'fix_coherence_low' => 'Coerenza bassa — suggerimento di riorganizzazione generato',
    'fix_coherence_ok' => 'Collezione coerente — nessun intervento necessario',
    'fix_coherence_error' => 'Errore durante l\'analisi di coerenza',
];
