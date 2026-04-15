<?php

/**
 * @package Lang\Fr
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-14
 * @purpose Traductions françaises La Bottega — SSOT backend i18n
 */

return [

    // ── Controller errors ──────────────────────────────────────────
    'profile_not_found' => 'Profil artiste non trouvé',
    'onboarding_already_completed' => 'Onboarding déjà terminé',

    // ── ProfileDiagnosticService — findings ────────────────────────
    'diag_medium_missing' => 'Médium principal non défini',
    'diag_statement_missing' => 'Artist statement manquant',
    'diag_bio_absent' => 'Bio absente — les collectionneurs la lisent avant d\'acheter',
    'diag_instagram_missing' => 'Instagram non configuré',
    'diag_few_artworks' => 'Œuvres insuffisantes — au moins 5 requises',
    'diag_no_artworks' => 'Aucune œuvre téléchargée',
    'diag_no_collections' => 'Aucune collection créée',
    'diag_no_prices' => 'Œuvres sans prix — nécessaires pour le Price Advisor',
    'diag_no_coa' => 'Aucun COA Sigillo émis — argument de vente fondamental',
    'diag_low_coherence' => 'Faible cohérence stylistique — exécutez Coherence Check',

    // ── MaestroDiBottegaService ────────────────────────────────────
    'maestro_not_connected' => '[Maestro pas encore connecté au service Python AI]',
    'maestro_temporarily_unavailable' => 'Le Maestro est temporairement indisponible. Veuillez réessayer dans un instant.',
    'maestro_empty_response' => 'Le Maestro n\'a pas généré de réponse. Veuillez réessayer.',
    'no_data_available' => 'Aucune donnée disponible.',
    'context_artworks' => 'Œuvres téléchargées',
    'context_collections' => 'Collections',
    'context_sales' => 'Ventes',
    'context_certifications' => 'Certifications blockchain',

    // ── ValutazioneIngressoService — strengths ─────────────────────
    'strength_identity' => 'Identité artistique bien définie',
    'strength_portfolio' => 'Portfolio bien fourni',
    'strength_visibility' => 'Bonne visibilité',

    // ── NextStepEngine — step labels (Percorso ZERO) ───────────────
    'step_1' => 'Choisis ton médium principal',
    'step_2' => 'Écris ton histoire d\'Origine',
    'step_3' => 'Définis ton style en une phrase',
    'step_4' => 'Télécharge tes 5 œuvres fondatrices',
    'step_5' => 'Définis ta logique de prix',
    'step_6' => 'Optimise ton profil public FlorenceEGI',
    'step_7' => 'Configure Instagram comme canal de découverte',
    'step_8' => 'Première story Instagram avec lien FlorenceEGI',
    'step_9' => 'Construis ta première liste email — les 50 premiers contacts',
    'step_10' => 'Identifie tes 10 premiers collectionneurs potentiels',
    'step_11' => 'Le message direct',
    'step_12' => 'Première vente — documente-la et célèbre-la',
    'step_13' => 'Première newsletter mensuelle',
    'step_14' => 'Identifie premier architecte d\'intérieur ou espace commercial',
    'step_15' => 'Deuxième collection avec cohérence vérifiée',
    'step_16' => 'Première EGI Credential',

    // ── NextStepEngine — fase labels ───────────────────────────────
    'fase_1' => 'Identité',
    'fase_2' => 'Présence Numérique',
    'fase_3' => 'Première Vente',
    'fase_4' => 'Construire le Rythme',
    'fase_completed' => 'Terminé',
    'percorso_completed' => 'Parcours terminé — profil excellent',

    // ── MicroscopioService — findings & actions ────────────────────
    'microscopio_error' => 'Erreur lors de l\'analyse Microscopio',
    'traits_low_coherence' => 'Cohérence des traits très faible entre les œuvres — identité visuelle fragmentée',
    'traits_moderate_coherence' => 'Cohérence des traits modérée — quelques éléments récurrents, mais plus de cohésion nécessaire',
    'weak_descriptions' => 'Descriptions d\'œuvres absentes ou trop courtes',
    'untitled_artwork' => 'Œuvre sans titre',

    // ── MicroscopioService — action labels ─────────────────────────
    'action_complete_bio' => 'Complète ta bio',
    'action_write_statement' => 'Écris ton artist statement',
    'action_upload_artwork' => 'Télécharge une œuvre',
    'action_set_prices' => 'Fixe tes prix',
    'action_improve_descriptions' => 'Améliore les descriptions',
    'action_coherence_check' => 'Exécuter Coherence Check',

    // ── MicroscopioService — fix NPE results ──────────────────────
    'fix_no_weak_descriptions' => 'Toutes les descriptions sont déjà adéquates',
    'fix_descriptions_sent' => 'Descriptions envoyées au Council NPE pour régénération',
    'fix_descriptions_error' => 'Erreur lors de la régénération des descriptions',
    'fix_pricing_complete' => 'Analyse des prix terminée',
    'fix_pricing_error' => 'Erreur lors de l\'analyse des prix',
    'fix_coherence_low' => 'Cohérence faible — suggestion de réorganisation générée',
    'fix_coherence_ok' => 'Collection cohérente — aucune action nécessaire',
    'fix_coherence_error' => 'Erreur lors de l\'analyse de cohérence',

    // ── SEO ───────────────────────────────────────────────────────────
    'meta_description' => 'Outils objectifs pour artistes et collectionneurs. Diagnostic, parcours de croissance et évaluation éclairée sur FlorenceEGI.',

    // ── Context LLM ───────────────────────────────────────────────────
    'context_percorso' => 'Parcours',
    'context_completeness' => 'Complétude du profil',
    'context_next_step' => 'Prochaine étape',
    'no_data_available' => 'Aucune donnée disponible',
];
