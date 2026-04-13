/**
 * @package La Bottega — AuthGuard
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Gate autenticazione — mostra login CTA se non autenticato
 */

import { type ReactNode } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { t } from '@/i18n/translations';

const EGI_LOGIN_URL = 'https://art.florenceegi.com/login?redirect=https://la-bottega.florenceegi.com';

export function AuthGuard({ children }: { children: ReactNode }) {
    const { user, loading, authenticated } = useAuth();

    if (loading) {
        return <LoadingScreen />;
    }

    if (!authenticated || !user) {
        return <LoginScreen />;
    }

    return <>{children}</>;
}

function LoadingScreen() {
    return (
        <div className="min-h-screen bg-bottega-navy flex items-center justify-center">
            <div className="text-center">
                <div className="w-16 h-16 mx-auto mb-6 rounded-full border-2 border-bottega-gold/30 flex items-center justify-center">
                    <div className="w-8 h-8 rounded-full border-2 border-bottega-gold border-t-transparent animate-spin" />
                </div>
                <p className="text-bottega-gold/60 text-sm tracking-widest uppercase">
                    {t('auth.loading')}
                </p>
            </div>
        </div>
    );
}

function LoginScreen() {
    return (
        <div className="min-h-screen bg-bottega-navy flex flex-col items-center justify-center px-6">
            <div className="max-w-md text-center">
                {/* Logo mark */}
                <div className="w-24 h-24 mx-auto mb-8 rounded-full border border-bottega-gold/40 flex items-center justify-center relative overflow-hidden">
                    <div className="absolute inset-0 bg-gradient-to-br from-bottega-gold/10 to-transparent" />
                    <svg className="w-12 h-12 text-bottega-gold relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1}>
                        <path strokeLinecap="round" strokeLinejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                    </svg>
                </div>

                <h1 className="text-3xl font-light text-white tracking-wide mb-2">
                    {t('layout.title')}
                </h1>
                <div className="w-12 h-px bg-bottega-gold mx-auto mb-6" />
                <p className="text-gray-400 mb-10 leading-relaxed">
                    {t('auth.login_required')}
                </p>

                <a
                    href={EGI_LOGIN_URL}
                    className="inline-flex items-center gap-3 px-8 py-3.5 bg-bottega-gold/10 border border-bottega-gold/40 text-bottega-gold rounded-lg hover:bg-bottega-gold/20 hover:border-bottega-gold/60 transition-all duration-300 text-sm tracking-wide uppercase"
                >
                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.5}>
                        <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                    {t('auth.login_cta')}
                </a>
            </div>
        </div>
    );
}
