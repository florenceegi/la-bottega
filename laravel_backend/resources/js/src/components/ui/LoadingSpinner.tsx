/**
 * @package La Bottega — LoadingSpinner
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.1.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Spinner oro animato — usato in loading states
 */

import { t } from '@/i18n/translations';

export function LoadingSpinner({ size = 'md' }: { size?: 'sm' | 'md' | 'lg' }) {
    const sizeClasses = { sm: 'w-4 h-4', md: 'w-8 h-8', lg: 'w-12 h-12' };

    return (
        <div className={`${sizeClasses[size]} animate-spin`} role="status" aria-label={t('a11y.loading')}>
            <svg viewBox="0 0 24 24" fill="none" className="text-bottega-gold">
                <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="2" opacity="0.2" />
                <path
                    d="M12 2a10 10 0 0 1 10 10"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                />
            </svg>
        </div>
    );
}
