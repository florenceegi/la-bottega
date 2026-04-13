/**
 * @package La Bottega — usePercorso Hook
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Hook stato percorso corrente — fase, step, completamenti
 */

import { useCallback, useEffect, useState } from 'react';
import { bottegaApi, type PercorsoStatus, type NextStepResponse } from '@/api/bottegaApi';

interface PercorsoState {
    status: PercorsoStatus | null;
    nextStep: NextStepResponse | null;
    loading: boolean;
    refresh: () => Promise<void>;
}

export function usePercorso(): PercorsoState {
    const [status, setStatus] = useState<PercorsoStatus | null>(null);
    const [nextStep, setNextStep] = useState<NextStepResponse | null>(null);
    const [loading, setLoading] = useState(true);

    const refresh = useCallback(async () => {
        setLoading(true);
        try {
            const [s, ns] = await Promise.all([
                bottegaApi.percorsoStatus(),
                bottegaApi.maestroNextStep(),
            ]);
            setStatus(s);
            setNextStep(ns);
        } catch {
            // No percorso yet — first visit
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => { refresh(); }, [refresh]);

    return { status, nextStep, loading, refresh };
}
