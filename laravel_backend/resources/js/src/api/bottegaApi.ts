/**
 * @package La Bottega — API Client
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Client HTTP per API La Bottega — Sanctum SSO cookie auth
 */

const API_BASE = '/api';

async function request<T>(path: string, options: RequestInit = {}): Promise<T> {
    const csrfToken = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content;

    const res = await fetch(`${API_BASE}${path}`, {
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            ...(options.headers ?? {}),
        },
        ...options,
    });

    if (res.status === 401) {
        throw new AuthError('Unauthorized');
    }

    if (!res.ok) {
        const body = await res.json().catch(() => ({}));
        throw new ApiError(res.status, body.message ?? 'Request failed');
    }

    return res.json();
}

export class AuthError extends Error {
    constructor(message: string) {
        super(message);
        this.name = 'AuthError';
    }
}

export class ApiError extends Error {
    constructor(public status: number, message: string) {
        super(message);
        this.name = 'ApiError';
    }
}

// --- Types ---

export interface User {
    id: number;
    name: string;
    email: string;
    roles?: string[];
}

export interface NextStepResponse {
    percorso: string;
    fase: number;
    step_number: number;
    step_key: string;
    title: string;
    description: string;
    action_type: string;
    field_check: string | null;
    status: string;
    completions_by_fase: Record<string, { completed: number; total: number }>;
}

export interface ProfileDiagnostic {
    total_score: number;
    categories: {
        identity: number;
        completeness: number;
        coherence: number;
        visibility: number;
    };
    findings: Array<{
        priority: string;
        category: string;
        message: string;
        action: string;
    }>;
}

export interface OnboardingResult {
    diagnostic: ProfileDiagnostic;
    percorso_assigned: string;
    next_step: NextStepResponse;
    welcome_context: {
        strengths: string[];
        gaps: string[];
        first_step_description: string;
    };
}

export interface ChatMessage {
    role: 'user' | 'assistant';
    content: string;
    buttons?: ContextualButton[];
    timestamp?: string;
}

export interface ContextualButton {
    label: string;
    action: string;
    type: 'tool' | 'navigate' | 'inline';
    target?: string;
}

export interface PercorsoStatus {
    percorso: string | null;
    fase: number;
    step_number: number;
    completions_by_fase: Record<string, { completed: number; total: number }>;
    started_at: string | null;
    completed_at: string | null;
}

export interface MaestroHealth {
    status: 'ok' | 'degraded' | 'down';
    latency_ms?: number;
}

export interface ToolExecution {
    tool_name: string;
    last_used_at: string;
}

// --- API Methods ---

export const bottegaApi = {
    // Auth
    getUser: () => request<User>('/user'),

    // Maestro
    maestroHealth: () => request<MaestroHealth>('/maestro/health'),

    maestroChat: (message: string, context?: Record<string, unknown>) =>
        request<{ message: ChatMessage }>('/maestro/chat', {
            method: 'POST',
            body: JSON.stringify({ message, context }),
        }),

    maestroNextStep: () => request<NextStepResponse>('/maestro/next-step'),

    maestroProfileDiagnostic: () => request<ProfileDiagnostic>('/maestro/profile-diagnostic'),

    maestroOnboarding: () => request<OnboardingResult>('/maestro/onboarding', {
        method: 'POST',
    }),

    // Percorso
    percorsoStatus: () => request<PercorsoStatus>('/percorso/status'),

    percorsoCompleteStep: (stepNumber: number, percorso: string) =>
        request<{ success: boolean }>('/percorso/complete-step', {
            method: 'POST',
            body: JSON.stringify({ step_number: stepNumber, percorso }),
        }),

    percorsoHistory: () => request<Array<{ step_number: number; fase: number; completed_at: string }>>('/percorso/history'),

    // Tools (GAP 1 — sidebar strumenti usati)
    toolsUnlocked: () => request<ToolExecution[]>('/tools/unlocked').catch(() => []),
};
