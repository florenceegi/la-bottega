/**
 * @package La Bottega — i18n System
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Atomic translation system — 6 lingue (P0-2, P0-9)
 */

import it from './it.json';
import en from './en.json';
import de from './de.json';
import es from './es.json';
import fr from './fr.json';
import pt from './pt.json';

export type Locale = 'it' | 'en' | 'de' | 'es' | 'fr' | 'pt';
export type TranslationKey = keyof typeof it;

const translations: Record<Locale, Record<string, string>> = { it, en, de, es, fr, pt };

const STORAGE_KEY = 'bottega_locale';
const DEFAULT_LOCALE: Locale = 'it';

export function detectLocale(): Locale {
    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored && stored in translations) return stored as Locale;

    const browserLang = navigator.language.slice(0, 2);
    if (browserLang in translations) return browserLang as Locale;

    return DEFAULT_LOCALE;
}

export function setLocale(locale: Locale): void {
    localStorage.setItem(STORAGE_KEY, locale);
    document.documentElement.lang = locale;
}

export function t(key: string, locale: Locale): string {
    return translations[locale]?.[key] ?? translations[DEFAULT_LOCALE]?.[key] ?? key;
}

export { translations };
