/**
 * @package La Bottega — CareerSummary
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Sidebar riepilogo carriera + CommunityPulse (FEAT 2) — indicatore minimale
 */

import { useEffect, useState } from 'react';
import { bottegaApi, type ProfileDiagnostic, type NextStepResponse, type PercorsoStatus } from '@/api/bottegaApi';
import { useTranslation } from '@/hooks/useTranslation';

interface Props {
    percorso: PercorsoStatus | null;
}

export function CareerSummary({ percorso }: Props) {
    const { t } = useTranslation();
    const [diagnostic, setDiagnostic] = useState<ProfileDiagnostic | null>(null);
    const [nextStep, setNextStep] = useState<NextStepResponse | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        Promise.all([
            bottegaApi.maestroProfileDiagnostic().catch(() => null),
            bottegaApi.maestroNextStep().catch(() => null),
        ]).then(([d, ns]) => {
            setDiagnostic(d);
            setNextStep(ns);
            setLoading(false);
        });
    }, []);

    if (loading) {
        return (
            <div className="p-5 space-y-4 overflow-y-auto" style={{ scrollbarWidth: 'thin', scrollbarColor: 'rgba(212,175,55,0.15) transparent' }}>
                <div className="h-4 w-24 bg-white/5 rounded bottega-shimmer" />
                <div className="h-20 bg-white/5 rounded-xl bottega-shimmer" />
                <div className="h-16 bg-white/5 rounded-xl bottega-shimmer" />
            </div>
        );
    }

    return (
        <div className="flex-1 p-5 space-y-6 overflow-y-auto" style={{ scrollbarWidth: 'thin', scrollbarColor: 'rgba(212,175,55,0.15) transparent' }}>
            <h3 className="text-[10px] text-bottega-gold/40 uppercase tracking-[0.2em] font-medium">
                {t('career.title')}
            </h3>

            {/* Completeness ring */}
            {diagnostic && (
                <div className="flex items-center gap-4">
                    <CompletenessRing score={diagnostic.total_score} />
                    <div>
                        <p className="text-white text-sm font-medium">{diagnostic.total_score}<span className="text-gray-500 text-xs">/100</span></p>
                        <p className="text-gray-500 text-[10px] uppercase tracking-wider">{t('career.completeness')}</p>
                    </div>
                </div>
            )}

            {/* Score categories */}
            {diagnostic && (
                <div className="grid grid-cols-2 gap-2">
                    <ScoreCard label={t('percorso.fase_identita')} value={diagnostic.categories.identity} max={25} />
                    <ScoreCard label={t('career.completeness')} value={diagnostic.categories.completeness} max={25} />
                    <ScoreCard label={t('tools.coherence')} value={diagnostic.categories.coherence} max={25} />
                    <ScoreCard label={t('tools.visibility')} value={diagnostic.categories.visibility} max={25} />
                </div>
            )}

            {/* Current percorso name */}
            {percorso?.percorso && (
                <div className="text-center">
                    <span className="text-[10px] text-bottega-gold/50 uppercase tracking-widest">
                        {t(`percorso.${percorso.percorso}`)}
                    </span>
                </div>
            )}

            {/* Next step */}
            {nextStep && (
                <div className="bg-bottega-gold/[0.04] border border-bottega-gold/10 rounded-xl p-3">
                    <p className="text-[10px] text-bottega-gold/40 uppercase tracking-widest mb-1.5">
                        {t('career.next_step')}
                    </p>
                    <p className="text-sm text-white/80 leading-relaxed">
                        {nextStep.title}
                    </p>
                </div>
            )}

            {!nextStep && !loading && (
                <div className="bg-white/[0.02] border border-white/5 rounded-xl p-3 text-center">
                    <p className="text-xs text-gray-600">{t('career.no_next_step')}</p>
                </div>
            )}

            {/* Divider */}
            <div className="w-8 h-px bg-white/5" />

            {/* Community Pulse — FEAT 2 */}
            <CommunityPulse />
        </div>
    );
}

function CompletenessRing({ score }: { score: number }) {
    const r = 18;
    const circumference = 2 * Math.PI * r;
    const offset = circumference - (score / 100) * circumference;
    const color = score >= 70 ? '#D4AF37' : score >= 40 ? '#E5C76B' : '#8B7424';

    return (
        <svg width="48" height="48" viewBox="0 0 48 48" className="flex-shrink-0" aria-hidden="true">
            <circle cx="24" cy="24" r={r} fill="none" stroke="rgba(255,255,255,0.05)" strokeWidth="3" />
            <circle
                cx="24" cy="24" r={r} fill="none" stroke={color} strokeWidth="3"
                strokeLinecap="round" strokeDasharray={circumference} strokeDashoffset={offset}
                transform="rotate(-90 24 24)"
                className="transition-all duration-1000 ease-out"
            />
        </svg>
    );
}

function ScoreCard({ label, value, max }: { label: string; value: number; max: number }) {
    const pct = Math.round((value / max) * 100);
    return (
        <div className="bg-white/[0.02] border border-white/5 rounded-lg p-2.5">
            <div className="flex items-baseline justify-between mb-1.5">
                <span className="text-[9px] text-gray-500 uppercase tracking-wider truncate">{label}</span>
                <span className="text-[10px] text-white/50 ml-1">{value}</span>
            </div>
            <div className="h-0.5 bg-white/5 rounded-full overflow-hidden">
                <div className="h-full bg-bottega-gold/40 rounded-full transition-all duration-700 ease-out" style={{ width: `${pct}%` }} />
            </div>
        </div>
    );
}

function CommunityPulse() {
    const { t } = useTranslation();
    const [stats, setStats] = useState<{ artists_completed: number; works_certified: number } | null>(null);

    useEffect(() => {
        // TODO: endpoint /api/community/stats — static placeholder for now
        setStats({ artists_completed: 12, works_certified: 347 });
    }, []);

    if (!stats) return null;

    return (
        <div className="space-y-2.5">
            <h4 className="text-[10px] text-gray-600 uppercase tracking-widest">
                Community
            </h4>
            <p className="text-[11px] text-gray-500 leading-relaxed">
                <span className="text-bottega-gold-light font-medium">{stats.artists_completed}</span>{' '}
                {t('community.artists_completed')}
            </p>
            <p className="text-[11px] text-gray-500 leading-relaxed">
                <span className="text-bottega-gold-light font-medium">{stats.works_certified}</span>{' '}
                {t('community.works_certified')}
            </p>
        </div>
    );
}
