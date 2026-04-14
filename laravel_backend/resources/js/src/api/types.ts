/**
 * @package La Bottega — API Types
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Type definitions per API responses La Bottega
 */

export interface User {
    id: number;
    name: string;
    email: string;
    roles?: string[];
}

export interface ArtistProfile {
    id: number;
    user_id: number;
    medium_primary: string | null;
    artist_statement_short: string | null;
    percorso_current: 'zero' | 'crescita' | 'mercato' | 'annuale' | null;
    profile_completeness_score: number;
    coherence_score: number;
    credibility_score: number;
    onboarding_completed_at: string | null;
}

export interface PercorsoStatus {
    percorso: string | null;
    fase: number;
    fase_name: string;
    steps_completed: number;
    steps_total: number;
    next_step: NextStep | null;
    completions_by_fase: Record<number, { completed: number; total: number }>;
}

export interface NextStep {
    step_number: number;
    fase: number;
    title: string;
    description: string;
    action_type: string;
    field_check: string | null;
}

export interface ChatMessage {
    id: string;
    role: 'user' | 'assistant';
    content: string;
    buttons?: ContextualButton[];
    timestamp: number;
}

export interface ContextualButton {
    label: string;
    action: string;
    type: 'tool' | 'deeplink' | 'inline';
    url?: string;
    tool_name?: string;
}

export interface ProfileDiagnostic {
    total_score: number;
    scores: {
        identity: number;
        completeness: number;
        coherence: number;
        visibility: number;
    };
    findings: DiagnosticFinding[];
}

export interface DiagnosticFinding {
    category: string;
    priority: 'critical' | 'high' | 'medium' | 'low';
    message: string;
    action: string | null;
}

export interface OnboardingResult {
    diagnostic: ProfileDiagnostic;
    percorso_assigned: string;
    next_step: NextStep;
    welcome_context: {
        strengths: string[];
        gaps: string[];
        first_step_description: string;
    };
}

export interface MaestroHealth {
    status: 'ok' | 'degraded' | 'down';
    latency_ms: number;
}

export interface ToolExecution {
    id: number;
    tool_name: string;
    created_at: string;
}
