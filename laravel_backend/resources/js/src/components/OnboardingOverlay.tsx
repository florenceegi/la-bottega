/**
 * @package La Bottega — OnboardingOverlay
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Momento WOW primo accesso (GAP 3a) — il Maestro appare e "legge" il profilo
 */

import { useEffect, useRef, useState } from 'react';
import { bottegaApi, type User, type OnboardingResult } from '@/api/bottegaApi';
import { useTranslation } from '@/hooks/useTranslation';
import { GoldButton } from '@/components/ui/GoldButton';
import DOMPurify from 'dompurify';

interface Props {
    user: User;
    onComplete: (result: OnboardingResult) => void;
}

type Phase = 'entrance' | 'reading' | 'analyzing' | 'result';

export function OnboardingOverlay({ user, onComplete }: Props) {
    const { t } = useTranslation();
    const [phase, setPhase] = useState<Phase>('entrance');
    const [result, setResult] = useState<OnboardingResult | null>(null);
    const [revealedLines, setRevealedLines] = useState<string[]>([]);
    const hasStarted = useRef(false);
    const overlayRef = useRef<HTMLDivElement>(null);

    // Phase progression: entrance → reading → analyzing → result
    useEffect(() => {
        if (phase === 'entrance') {
            const timer = setTimeout(() => {
                setPhase('reading');
                if (!hasStarted.current) {
                    hasStarted.current = true;
                    bottegaApi.maestroOnboarding()
                        .then(setResult)
                        .catch(() => {
                            // Graceful fallback — skip onboarding
                            onComplete({
                                diagnostic: { total_score: 0, categories: { identity: 0, completeness: 0, coherence: 0, visibility: 0 }, findings: [] },
                                percorso_assigned: 'zero',
                                next_step: {} as OnboardingResult['next_step'],
                                welcome_context: { strengths: [], gaps: [], first_step_description: '' },
                            });
                        });
                }
            }, 2000);
            return () => clearTimeout(timer);
        }
    }, [phase, onComplete]);

    useEffect(() => {
        if (phase === 'reading') {
            const timer = setTimeout(() => setPhase('analyzing'), 2500);
            return () => clearTimeout(timer);
        }
    }, [phase]);

    useEffect(() => {
        if (result && phase === 'analyzing') {
            const timer = setTimeout(() => setPhase('result'), 1500);
            return () => clearTimeout(timer);
        }
    }, [result, phase]);

    // Progressive text reveal
    useEffect(() => {
        if (phase !== 'result' || !result) return;

        const lines = [
            ...result.welcome_context.strengths.map(s => `✦ ${s}`),
            '',
            ...result.welcome_context.gaps.map(g => `○ ${g}`),
        ];

        let idx = 0;
        const interval = setInterval(() => {
            if (idx < lines.length) {
                setRevealedLines(prev => [...prev, lines[idx]]);
                idx++;
            } else {
                clearInterval(interval);
            }
        }, 400);
        return () => clearInterval(interval);
    }, [phase, result]);

    // Append to body for Safari iOS (trappola position:fixed dentro overflow:hidden)
    useEffect(() => {
        const el = overlayRef.current;
        if (el) document.body.appendChild(el);
        return () => { if (el?.parentNode === document.body) document.body.removeChild(el); };
    }, []);

    const allLinesRevealed = result
        ? revealedLines.length >= (result.welcome_context.strengths.length + result.welcome_context.gaps.length + 1)
        : false;

    return (
        <div
            ref={overlayRef}
            className="fixed inset-0 z-50 bg-bottega-navy-dark/95 backdrop-blur-sm flex items-center justify-center p-6"
            role="dialog"
            aria-modal="true"
            aria-label={t('maestro.onboarding_title')}
        >
            <div className="max-w-md w-full text-center animate-scale-in">
                {/* Maestro avatar */}
                <div className={`w-28 h-28 mx-auto mb-10 rounded-full border-2 flex items-center justify-center relative transition-all duration-1000 ${
                    phase === 'entrance'
                        ? 'border-bottega-gold/20 scale-90 opacity-0'
                        : 'border-bottega-gold/50 scale-100 opacity-100 animate-pulse-gold'
                }`}>
                    <div className="absolute inset-0 rounded-full bg-gradient-to-br from-bottega-gold/10 via-transparent to-bottega-gold/5" />
                    <svg className="w-14 h-14 text-bottega-gold relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={0.8}>
                        <path strokeLinecap="round" strokeLinejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                    </svg>
                </div>

                {/* Phase: entrance */}
                {phase === 'entrance' && (
                    <div className="animate-fade-up">
                        <h2 className="text-2xl font-light text-white tracking-wide mb-3">
                            {t('onboarding.step1_title')}
                        </h2>
                        <div className="w-12 h-px bg-gradient-to-r from-transparent via-bottega-gold to-transparent mx-auto mb-4" />
                        <p className="text-gray-400 text-sm">
                            {t('onboarding.step1_text')}
                        </p>
                    </div>
                )}

                {/* Phase: reading / analyzing */}
                {(phase === 'reading' || phase === 'analyzing') && (
                    <div className="animate-fade-up">
                        <h2 className="text-xl font-light text-white tracking-wide mb-6">
                            {t('maestro.onboarding_title')}
                        </h2>
                        <div className="space-y-4 text-left max-w-xs mx-auto">
                            <PhaseStep label={t('maestro.onboarding_reading')} active={phase === 'reading'} done={phase === 'analyzing'} />
                            <PhaseStep label={t('onboarding.analyzing')} active={phase === 'analyzing'} done={false} />
                        </div>
                    </div>
                )}

                {/* Phase: result — progressive reveal */}
                {phase === 'result' && result && (
                    <div className="animate-fade-up text-left max-w-xs mx-auto">
                        <div className="mb-6 text-center">
                            <p className="text-bottega-gold text-[10px] uppercase tracking-[0.2em] mb-2">
                                {t(`percorso.${result.percorso_assigned}`)}
                            </p>
                            <div className="flex items-baseline justify-center gap-1">
                                <span className="text-3xl font-light text-white">{result.diagnostic.total_score}</span>
                                <span className="text-gray-500 text-xs">/100</span>
                            </div>
                        </div>

                        <div className="space-y-1.5 mb-8 min-h-[100px]">
                            {revealedLines.map((line, i) => (
                                <p
                                    key={i}
                                    className={`text-sm animate-fade-up ${
                                        line.startsWith('✦') ? 'text-bottega-gold-light'
                                            : line.startsWith('○') ? 'text-gray-400'
                                            : 'h-2'
                                    }`}
                                    style={{ animationDelay: `${i * 0.05}s` }}
                                    dangerouslySetInnerHTML={{ __html: DOMPurify.sanitize(line) }}
                                />
                            ))}
                        </div>

                        {allLinesRevealed && (
                            <div className="text-center animate-fade-up" style={{ animationDelay: '0.3s' }}>
                                <GoldButton onClick={() => onComplete(result)}>
                                    {t('onboarding.complete')}
                                </GoldButton>
                            </div>
                        )}
                    </div>
                )}
            </div>
        </div>
    );
}

function PhaseStep({ label, active, done }: { label: string; active: boolean; done: boolean }) {
    return (
        <div className="flex items-center gap-3">
            <div className={`w-6 h-6 rounded-full border flex items-center justify-center transition-all duration-500 ${
                done ? 'border-bottega-gold bg-bottega-gold/20'
                    : active ? 'border-bottega-gold/60 animate-pulse-gold'
                    : 'border-white/10'
            }`}>
                {done && (
                    <svg className="w-3 h-3 text-bottega-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={3}>
                        <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                )}
                {active && !done && <div className="w-2 h-2 rounded-full bg-bottega-gold" />}
            </div>
            <span className={`text-sm transition-colors duration-300 ${
                done ? 'text-bottega-gold' : active ? 'text-white' : 'text-gray-600'
            }`}>
                {label}
            </span>
        </div>
    );
}
