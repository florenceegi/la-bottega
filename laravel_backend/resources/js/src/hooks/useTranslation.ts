/**
 * @package La Bottega — useTranslation Hook
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose React hook for atomic translations (P0-2, P0-9)
 */

import { useCallback, useSyncExternalStore } from 'react';
import { type Locale, detectLocale, setLocale as persistLocale, t as translate } from '@/i18n';

let currentLocale: Locale = detectLocale();
const listeners = new Set<() => void>();

function subscribe(cb: () => void) {
    listeners.add(cb);
    return () => listeners.delete(cb);
}

function getSnapshot(): Locale {
    return currentLocale;
}

export function useTranslation() {
    const locale = useSyncExternalStore(subscribe, getSnapshot);

    const t = useCallback((key: string) => translate(key, locale), [locale]);

    const changeLocale = useCallback((next: Locale) => {
        currentLocale = next;
        persistLocale(next);
        listeners.forEach(cb => cb());
    }, []);

    return { t, locale, changeLocale } as const;
}
