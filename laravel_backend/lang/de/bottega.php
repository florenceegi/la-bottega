<?php

/**
 * @package Lang\De
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-14
 * @purpose Deutsche Übersetzungen La Bottega — SSOT backend i18n
 */

return [

    // ── Controller errors ──────────────────────────────────────────
    'profile_not_found' => 'Künstlerprofil nicht gefunden',
    'onboarding_already_completed' => 'Onboarding bereits abgeschlossen',

    // ── ProfileDiagnosticService — findings ────────────────────────
    'diag_medium_missing' => 'Primäres Medium nicht definiert',
    'diag_statement_missing' => 'Artist Statement fehlt',
    'diag_bio_absent' => 'Bio fehlt — Sammler lesen sie vor dem Kauf',
    'diag_instagram_missing' => 'Instagram nicht konfiguriert',
    'diag_few_artworks' => 'Zu wenige Werke — mindestens 5 erforderlich',
    'diag_no_artworks' => 'Keine Werke hochgeladen',
    'diag_no_collections' => 'Keine Sammlungen erstellt',
    'diag_no_prices' => 'Werke ohne Preis — benötigt für den Price Advisor',
    'diag_no_coa' => 'Kein Sigillo COA ausgestellt — ein wichtiges Verkaufsargument',
    'diag_low_coherence' => 'Geringe stilistische Kohärenz — führe Coherence Check aus',

    // ── MaestroDiBottegaService ────────────────────────────────────
    'maestro_not_connected' => '[Maestro noch nicht mit Python AI Service verbunden]',
    'no_data_available' => 'Keine Daten verfügbar.',
    'context_artworks' => 'Hochgeladene Werke',
    'context_collections' => 'Sammlungen',
    'context_sales' => 'Verkäufe',
    'context_certifications' => 'Blockchain-Zertifizierungen',

    // ── ValutazioneIngressoService — strengths ─────────────────────
    'strength_identity' => 'Gut definierte künstlerische Identität',
    'strength_portfolio' => 'Gut bestücktes Portfolio',
    'strength_visibility' => 'Gute Sichtbarkeit',

    // ── NextStepEngine — step labels (Percorso ZERO) ───────────────
    'step_1' => 'Wähle dein primäres Medium',
    'step_2' => 'Schreibe deine Ursprungsgeschichte',
    'step_3' => 'Definiere deinen Stil in einem Satz',
    'step_4' => 'Lade deine 5 Gründungswerke hoch',
    'step_5' => 'Definiere deine Preislogik',
    'step_6' => 'Optimiere dein öffentliches FlorenceEGI-Profil',
    'step_7' => 'Richte Instagram als Entdeckungskanal ein',
    'step_8' => 'Erste Instagram-Story mit FlorenceEGI-Link',
    'step_9' => 'Erstelle deine erste E-Mail-Liste — die ersten 50 Kontakte',
    'step_10' => 'Identifiziere deine 10 potenziellen ersten Sammler',
    'step_11' => 'Die Direktnachricht',
    'step_12' => 'Erster Verkauf — dokumentiere und feiere ihn',
    'step_13' => 'Erster monatlicher Newsletter',
    'step_14' => 'Identifiziere ersten Innenarchitekten oder Geschäftsraum',
    'step_15' => 'Zweite Sammlung mit verifizierter Kohärenz',
    'step_16' => 'Erste EGI Credential',

    // ── NextStepEngine — fase labels ───────────────────────────────
    'fase_1' => 'Identität',
    'fase_2' => 'Digitale Präsenz',
    'fase_3' => 'Erster Verkauf',
    'fase_4' => 'Den Rhythmus aufbauen',
    'fase_completed' => 'Abgeschlossen',
    'percorso_completed' => 'Weg abgeschlossen — ausgezeichnetes Profil',

    // ── MicroscopioService — findings & actions ────────────────────
    'microscopio_error' => 'Fehler bei der Microscopio-Analyse',
    'traits_low_coherence' => 'Sehr geringe Trait-Kohärenz zwischen den Werken — fragmentierte visuelle Identität',
    'traits_moderate_coherence' => 'Mäßige Trait-Kohärenz — einige wiederkehrende Elemente, aber mehr Zusammenhalt nötig',
    'weak_descriptions' => 'Werkbeschreibungen fehlen oder sind zu kurz',
    'untitled_artwork' => 'Werk ohne Titel',

    // ── MicroscopioService — action labels ─────────────────────────
    'action_complete_bio' => 'Vervollständige deine Bio',
    'action_write_statement' => 'Schreibe dein Artist Statement',
    'action_upload_artwork' => 'Lade ein Werk hoch',
    'action_set_prices' => 'Lege deine Preise fest',
    'action_improve_descriptions' => 'Beschreibungen verbessern',
    'action_coherence_check' => 'Coherence Check ausführen',
];
