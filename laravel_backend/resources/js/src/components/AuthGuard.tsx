/**
 * @package La Bottega — AuthGuard
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-14
 * @purpose Gate autenticazione — login form in-app con Bearer token
 */

import { type ReactNode, useState, type FormEvent } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { t } from '@/i18n/translations';

export function AuthGuard({ children }: { children: ReactNode }) {
    const { user, loading, authenticated, login } = useAuth();

    if (loading) {
        return <LoadingScreen />;
    }

    if (!authenticated || !user) {
        return <LoginScreen onLogin={login} />;
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

interface LoginScreenProps {
    onLogin: (email: string, password: string) => Promise<void>;
}

function LoginScreen({ onLogin }: LoginScreenProps) {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [submitting, setSubmitting] = useState(false);

    const handleSubmit = async (e: FormEvent) => {
        e.preventDefault();
        setError('');
        setSubmitting(true);

        try {
            await onLogin(email, password);
        } catch {
            setError(t('auth.login_error'));
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <div className="min-h-screen bg-bottega-navy flex flex-col items-center justify-center px-6">
            <div className="w-full max-w-md">
                {/* Logo mark */}
                <div className="text-center mb-10">
                    <div className="w-24 h-24 mx-auto mb-8 rounded-full border border-bottega-gold/40 flex items-center justify-center relative overflow-hidden">
                        <div className="absolute inset-0 bg-gradient-to-br from-bottega-gold/10 to-transparent" />
                        <svg
                            className="w-12 h-12 text-bottega-gold relative z-10"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            strokeWidth={1}
                            aria-hidden="true"
                        >
                            <path strokeLinecap="round" strokeLinejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                        </svg>
                    </div>

                    <h1 className="text-3xl font-light text-white tracking-wide mb-2">
                        {t('layout.title')}
                    </h1>
                    <div className="w-12 h-px bg-bottega-gold mx-auto mb-4" />
                    <p className="text-gray-400 leading-relaxed">
                        {t('auth.login_required')}
                    </p>
                </div>

                {/* Login form */}
                <form onSubmit={handleSubmit} className="space-y-6">
                    {error && (
                        <div
                            className="px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-400 text-sm text-center"
                            role="alert"
                            aria-live="polite"
                        >
                            {error}
                        </div>
                    )}

                    <div>
                        <label
                            htmlFor="bottega-email"
                            className="block text-sm text-bottega-gold/80 mb-2 tracking-wide"
                        >
                            {t('auth.email_label')}
                        </label>
                        <input
                            id="bottega-email"
                            type="email"
                            required
                            autoComplete="email"
                            value={email}
                            onChange={e => setEmail(e.target.value)}
                            placeholder={t('auth.email_placeholder')}
                            disabled={submitting}
                            className="w-full px-4 py-3 rounded-lg bg-white/5 border border-bottega-gold/20 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-bottega-gold/50 focus:border-bottega-gold/40 transition-all duration-200 disabled:opacity-50"
                        />
                    </div>

                    <div>
                        <label
                            htmlFor="bottega-password"
                            className="block text-sm text-bottega-gold/80 mb-2 tracking-wide"
                        >
                            {t('auth.password_label')}
                        </label>
                        <input
                            id="bottega-password"
                            type="password"
                            required
                            autoComplete="current-password"
                            value={password}
                            onChange={e => setPassword(e.target.value)}
                            placeholder={t('auth.password_placeholder')}
                            disabled={submitting}
                            className="w-full px-4 py-3 rounded-lg bg-white/5 border border-bottega-gold/20 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-bottega-gold/50 focus:border-bottega-gold/40 transition-all duration-200 disabled:opacity-50"
                        />
                    </div>

                    <button
                        type="submit"
                        disabled={submitting}
                        className="w-full flex items-center justify-center gap-3 px-8 py-3.5 bg-bottega-gold/10 border border-bottega-gold/40 text-bottega-gold rounded-lg hover:bg-bottega-gold/20 hover:border-bottega-gold/60 transition-all duration-300 text-sm tracking-wide uppercase disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-bottega-gold/50"
                    >
                        {submitting ? (
                            <>
                                <div className="w-4 h-4 rounded-full border-2 border-bottega-gold border-t-transparent animate-spin" />
                                {t('auth.login_submitting')}
                            </>
                        ) : (
                            <>
                                <svg
                                    className="w-5 h-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    strokeWidth={1.5}
                                    aria-hidden="true"
                                >
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                </svg>
                                {t('auth.login_cta')}
                            </>
                        )}
                    </button>
                </form>
            </div>
        </div>
    );
}
