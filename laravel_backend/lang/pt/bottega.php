<?php

/**
 * @package Lang\Pt
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-14
 * @purpose Traduções em português La Bottega — SSOT backend i18n
 */

return [

    // ── Controller errors ──────────────────────────────────────────
    'profile_not_found' => 'Perfil de artista não encontrado',
    'onboarding_already_completed' => 'Onboarding já concluído',

    // ── ProfileDiagnosticService — findings ────────────────────────
    'diag_medium_missing' => 'Meio primário não definido',
    'diag_statement_missing' => 'Artist statement em falta',
    'diag_bio_absent' => 'Bio ausente — os colecionadores leem-na antes de comprar',
    'diag_instagram_missing' => 'Instagram não configurado',
    'diag_few_artworks' => 'Obras insuficientes — são necessárias pelo menos 5',
    'diag_no_artworks' => 'Nenhuma obra carregada',
    'diag_no_collections' => 'Nenhuma coleção criada',
    'diag_no_prices' => 'Obras sem preço — necessários para o Price Advisor',
    'diag_no_coa' => 'Nenhum COA Sigillo emitido — argumento de venda fundamental',
    'diag_low_coherence' => 'Baixa coerência estilística — execute Coherence Check',

    // ── MaestroDiBottegaService ────────────────────────────────────
    'maestro_not_connected' => '[Maestro ainda não conectado ao serviço Python AI]',
    'no_data_available' => 'Sem dados disponíveis.',
    'context_artworks' => 'Obras carregadas',
    'context_collections' => 'Coleções',
    'context_sales' => 'Vendas',
    'context_certifications' => 'Certificações blockchain',

    // ── ValutazioneIngressoService — strengths ─────────────────────
    'strength_identity' => 'Identidade artística bem definida',
    'strength_portfolio' => 'Portfólio bem fornecido',
    'strength_visibility' => 'Boa visibilidade',

    // ── NextStepEngine — step labels (Percorso ZERO) ───────────────
    'step_1' => 'Escolhe o teu meio primário',
    'step_2' => 'Escreve a tua história de Origem',
    'step_3' => 'Define o teu estilo numa frase',
    'step_4' => 'Carrega as tuas 5 obras fundadoras',
    'step_5' => 'Define a tua lógica de preços',
    'step_6' => 'Otimiza o teu perfil público FlorenceEGI',
    'step_7' => 'Configura o Instagram como canal de descoberta',
    'step_8' => 'Primeira story no Instagram com link FlorenceEGI',
    'step_9' => 'Constrói a tua primeira lista de email — os 50 contactos iniciais',
    'step_10' => 'Identifica os teus 10 potenciais primeiros colecionadores',
    'step_11' => 'A mensagem direta',
    'step_12' => 'Primeira venda — documenta-a e celebra-a',
    'step_13' => 'Primeira newsletter mensal',
    'step_14' => 'Identifica primeiro designer de interiores ou espaço comercial',
    'step_15' => 'Segunda coleção com coerência verificada',
    'step_16' => 'Primeira EGI Credential',

    // ── NextStepEngine — fase labels ───────────────────────────────
    'fase_1' => 'Identidade',
    'fase_2' => 'Presença Digital',
    'fase_3' => 'Primeira Venda',
    'fase_4' => 'Construir o Ritmo',
    'fase_completed' => 'Concluído',
    'percorso_completed' => 'Percurso concluído — perfil excelente',

    // ── MicroscopioService — findings & actions ────────────────────
    'microscopio_error' => 'Erro durante a análise Microscopio',
    'traits_low_coherence' => 'Coerência de traits muito baixa entre obras — identidade visual fragmentada',
    'traits_moderate_coherence' => 'Coerência de traits moderada — alguns elementos recorrentes, mas mais coesão necessária',
    'weak_descriptions' => 'Descrições de obras ausentes ou demasiado curtas',
    'untitled_artwork' => 'Obra sem título',

    // ── MicroscopioService — action labels ─────────────────────────
    'action_complete_bio' => 'Completa a tua bio',
    'action_write_statement' => 'Escreve o teu artist statement',
    'action_upload_artwork' => 'Carrega uma obra',
    'action_set_prices' => 'Define os teus preços',
    'action_improve_descriptions' => 'Melhora as descrições',
    'action_coherence_check' => 'Executar Coherence Check',

    // ── MicroscopioService — fix NPE results ──────────────────────
    'fix_no_weak_descriptions' => 'Todas as descrições já estão adequadas',
    'fix_descriptions_sent' => 'Descrições enviadas ao Council NPE para regeneração',
    'fix_descriptions_error' => 'Erro durante a regeneração das descrições',
    'fix_pricing_complete' => 'Análise de preços concluída',
    'fix_pricing_error' => 'Erro durante a análise de preços',
    'fix_coherence_low' => 'Coerência baixa — sugestão de reorganização gerada',
    'fix_coherence_ok' => 'Coleção coerente — nenhuma ação necessária',
    'fix_coherence_error' => 'Erro durante a análise de coerência',

    // ── SEO ───────────────────────────────────────────────────────────
    'meta_description' => 'Ferramentas objetivas para artistas e colecionadores. Diagnóstico, percursos de crescimento e avaliação informada na FlorenceEGI.',

    // ── Context LLM ───────────────────────────────────────────────────
    'context_percorso' => 'Percurso',
    'context_completeness' => 'Completude do perfil',
    'context_next_step' => 'Próximo passo',
    'no_data_available' => 'Sem dados disponíveis',
];
