/**
 * @package La Bottega — useMaestroHealth Hook
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Health check Maestro per fallback GAP 5 — polling ogni 30s
 */

import { useEffect, useState } from 'react';
import { bottegaApi, type MaestroHealth } from '@/api/bottegaApi';

export function useMaestroHealth() {
    const [health, setHealth] = useState<MaestroHealth>({ status: 'ok' });

    useEffect(() => {
        let cancelled = false;

        const check = () => {
            bottegaApi.maestroHealth()
                .then(h => { if (!cancelled) setHealth(h); })
                .catch(() => { if (!cancelled) setHealth({ status: 'down' }); });
        };

        check();
        const interval = setInterval(check, 30_000);

        return () => { cancelled = true; clearInterval(interval); };
    }, []);

    return health;
}
