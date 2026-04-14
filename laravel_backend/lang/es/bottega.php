<?php

/**
 * @package Lang\Es
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-14
 * @purpose Traducciones en español La Bottega — SSOT backend i18n
 */

return [

    // ── Controller errors ──────────────────────────────────────────
    'profile_not_found' => 'Perfil de artista no encontrado',
    'onboarding_already_completed' => 'Onboarding ya completado',

    // ── ProfileDiagnosticService — findings ────────────────────────
    'diag_medium_missing' => 'Medio primario no definido',
    'diag_statement_missing' => 'Artist statement faltante',
    'diag_bio_absent' => 'Bio ausente — los coleccionistas la leen antes de comprar',
    'diag_instagram_missing' => 'Instagram no configurado',
    'diag_few_artworks' => 'Obras insuficientes — se necesitan al menos 5',
    'diag_no_artworks' => 'Ninguna obra cargada',
    'diag_no_collections' => 'Ninguna colección creada',
    'diag_no_prices' => 'Obras sin precio — necesarios para el Price Advisor',
    'diag_no_coa' => 'Ningún COA Sigillo emitido — argumento de venta fundamental',
    'diag_low_coherence' => 'Baja coherencia estilística — ejecuta Coherence Check',

    // ── MaestroDiBottegaService ────────────────────────────────────
    'maestro_not_connected' => '[Maestro aún no conectado al servicio Python AI]',
    'no_data_available' => 'Sin datos disponibles.',
    'context_artworks' => 'Obras cargadas',
    'context_collections' => 'Colecciones',
    'context_sales' => 'Ventas',
    'context_certifications' => 'Certificaciones blockchain',

    // ── ValutazioneIngressoService — strengths ─────────────────────
    'strength_identity' => 'Identidad artística bien definida',
    'strength_portfolio' => 'Portfolio bien surtido',
    'strength_visibility' => 'Buena visibilidad',

    // ── NextStepEngine — step labels (Percorso ZERO) ───────────────
    'step_1' => 'Elige tu medio primario',
    'step_2' => 'Escribe tu historia de Origen',
    'step_3' => 'Define tu estilo en una frase',
    'step_4' => 'Sube tus 5 obras fundacionales',
    'step_5' => 'Define tu lógica de precios',
    'step_6' => 'Optimiza tu perfil público en FlorenceEGI',
    'step_7' => 'Configura Instagram como canal de descubrimiento',
    'step_8' => 'Primera historia de Instagram con enlace a FlorenceEGI',
    'step_9' => 'Construye tu primera lista de email — los 50 contactos iniciales',
    'step_10' => 'Identifica a tus 10 potenciales primeros coleccionistas',
    'step_11' => 'El mensaje directo',
    'step_12' => 'Primera venta — documéntala y celébrala',
    'step_13' => 'Primera newsletter mensual',
    'step_14' => 'Identifica primer diseñador de interiores o espacio comercial',
    'step_15' => 'Segunda colección con coherencia verificada',
    'step_16' => 'Primera credencial EGI Credential',

    // ── NextStepEngine — fase labels ───────────────────────────────
    'fase_1' => 'Identidad',
    'fase_2' => 'Presencia Digital',
    'fase_3' => 'Primera Venta',
    'fase_4' => 'Construir el Ritmo',
    'fase_completed' => 'Completado',
    'percorso_completed' => 'Recorrido completado — perfil excelente',

    // ── MicroscopioService — findings & actions ────────────────────
    'microscopio_error' => 'Error durante el análisis Microscopio',
    'traits_low_coherence' => 'Coherencia de traits muy baja entre obras — identidad visual fragmentada',
    'traits_moderate_coherence' => 'Coherencia de traits moderada — algunos elementos recurrentes, pero se necesita más cohesión',
    'weak_descriptions' => 'Descripciones de obras ausentes o demasiado cortas',
    'untitled_artwork' => 'Obra sin título',

    // ── MicroscopioService — action labels ─────────────────────────
    'action_complete_bio' => 'Completa tu bio',
    'action_write_statement' => 'Escribe tu artist statement',
    'action_upload_artwork' => 'Sube una obra',
    'action_set_prices' => 'Establece tus precios',
    'action_improve_descriptions' => 'Mejora las descripciones',
    'action_coherence_check' => 'Ejecutar Coherence Check',

    // ── MicroscopioService — fix NPE results ──────────────────────
    'fix_no_weak_descriptions' => 'Todas las descripciones ya son adecuadas',
    'fix_descriptions_sent' => 'Descripciones enviadas al Council NPE para regeneración',
    'fix_descriptions_error' => 'Error durante la regeneración de descripciones',
    'fix_pricing_complete' => 'Análisis de precios completado',
    'fix_pricing_error' => 'Error durante el análisis de precios',
    'fix_coherence_low' => 'Coherencia baja — sugerencia de reorganización generada',
    'fix_coherence_ok' => 'Colección coherente — ninguna acción necesaria',
    'fix_coherence_error' => 'Error durante el análisis de coherencia',
];
