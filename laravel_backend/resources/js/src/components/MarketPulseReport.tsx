/**
 * @package La Bottega — MarketPulseReport
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Report Market Pulse — segnali di mercato + sintesi vendite artista.
 */

import { useCallback, useEffect, useState } from 'react';
import {
    bottegaApi,
    type MarketPulseReport as MarketPulseReportType,
    type MarketTrendSignal,
    type MarketPulseSalesSummary,
} from '@/api/bottegaApi';
import { useTranslation } from '@/hooks/useTranslation';

interface Props {
    open: boolean;
    onClose: () => void;
}

const CAREER_KEYS: Record<string, string> = {
    emerging: 'binocolo.career_emerging',
    mid: 'binocolo.career_mid',
    established: 'binocolo.career_established',
};

const DIRECTION_KEYS: Record<string, string> = {
    rising: 'market_pulse.direction_rising',
    stable: 'market_pulse.direction_stable',
    declining: 'market_pulse.direction_declining',
};

const CATEGORY_KEYS: Record<string, string> = {
    demand: 'market_pulse.category_demand',
    price: 'market_pulse.category_price',
    opportunity: 'market_pulse.category_opportunity',
};

export function MarketPulseReport({ open, onClose }: Props) {
    const { t } = useTranslation();
    const [report, setReport] = useState<MarketPulseReportType | null>(null);
    const [loading, setLoading] = useState(false);

    const runPulse = useCallback(async () => {
        setLoading(true);
        try {
            const { data } = await bottegaApi.marketPulse();
            setReport(data);
        } catch {
            setReport(null);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        if (open && !report && !loading) {
            runPulse();
        }
    }, [open, report, loading, runPulse]);

    if (!open) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-label={t('market_pulse.title')}>
            <div className="absolute inset-0 bg-black/60 backdrop-blur-sm" onClick={onClose} />

            <div
                className="relative w-full max-w-3xl max-h-[85vh] overflow-y-auto bg-bottega-navy border border-white/10 rounded-2xl shadow-2xl"
                style={{ scrollbarWidth: 'thin', scrollbarColor: 'rgba(212,175,55,0.15) transparent' }}
            >
                <div className="sticky top-0 z-10 bg-bottega-navy/95 backdrop-blur-sm border-b border-white/5 px-6 py-4 flex items-center justify-between">
                    <div>
                        <h2 className="text-lg font-light text-white">{t('market_pulse.title')}</h2>
                        <p className="text-xs text-gray-500 mt-0.5">{t('market_pulse.subtitle')}</p>
                    </div>
                    <button
                        onClick={onClose}
                        className="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 hover:text-white hover:bg-white/5 transition-colors"
                        aria-label={t('market_pulse.close')}
                    >
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div className="p-6 space-y-6">
                    {loading ? (
                        <LoadingState label={t('market_pulse.running')} />
                    ) : report ? (
                        <>
                            <Overview report={report} />
                            <SalesBlock summary={report.sales_summary} />
                            <SignalsBlock signals={report.signals} />
                        </>
                    ) : (
                        <div className="text-center py-8">
                            <button
                                onClick={runPulse}
                                className="px-6 py-3 rounded-xl bg-bottega-gold/10 border border-bottega-gold/30 text-bottega-gold hover:bg-bottega-gold/20 transition-all"
                            >
                                {t('market_pulse.run')}
                            </button>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}

function LoadingState({ label }: { label: string }) {
    return (
        <div className="flex flex-col items-center py-12 gap-4 animate-fade-up">
            <div className="w-16 h-16 rounded-full border-2 border-bottega-gold/30 flex items-center justify-center">
                <div className="w-8 h-8 rounded-full border-2 border-bottega-gold border-t-transparent animate-spin" />
            </div>
            <p className="text-sm text-gray-400">{label}</p>
        </div>
    );
}

function Overview({ report }: { report: MarketPulseReportType }) {
    const { t } = useTranslation();
    const careerLabel = t(CAREER_KEYS[report.career_level] ?? report.career_level);

    return (
        <div className="grid grid-cols-2 gap-3 animate-fade-up">
            <div className="bg-white/[0.03] border border-white/5 rounded-xl px-4 py-3">
                <p className="text-[10px] uppercase tracking-wider text-gray-500">{t('market_pulse.primary_medium')}</p>
                <p className="text-sm text-white mt-0.5">{report.medium_primary ?? '—'}</p>
            </div>
            <div className="bg-white/[0.03] border border-white/5 rounded-xl px-4 py-3">
                <p className="text-[10px] uppercase tracking-wider text-gray-500">{t('market_pulse.career_level')}</p>
                <p className="text-sm text-white mt-0.5">{careerLabel}</p>
            </div>
        </div>
    );
}

function SalesBlock({ summary }: { summary: MarketPulseSalesSummary }) {
    const { t } = useTranslation();

    return (
        <section className="animate-fade-up">
            <h3 className="text-sm font-medium text-white mb-3">{t('market_pulse.sales_title')}</h3>

            {!summary.has_data ? (
                <div className="bg-amber-400/5 border border-amber-400/20 rounded-xl px-4 py-6 text-center text-sm text-amber-400">
                    {t('market_pulse.no_sales')}
                </div>
            ) : (
                <div className="space-y-3">
                    <div className="grid grid-cols-2 gap-3">
                        <Stat label={t('market_pulse.total_sales')} value={String(summary.sales_count)} />
                        <Stat label={t('market_pulse.total_revenue')} value={`€ ${summary.total_amount.toFixed(2)}`} />
                    </div>

                    {summary.by_medium.length > 0 && (
                        <div>
                            <p className="text-[10px] uppercase tracking-wider text-gray-500 mb-2">{t('market_pulse.by_medium')}</p>
                            <ul className="space-y-1.5">
                                {summary.by_medium.map((b) => (
                                    <li key={b.medium} className="flex items-center justify-between bg-white/[0.03] border border-white/5 rounded-lg px-3 py-2 text-xs">
                                        <span className={b.is_primary ? 'text-bottega-gold' : 'text-gray-300'}>
                                            {b.medium}
                                            {b.is_primary && <span className="ml-1.5 text-[10px] uppercase tracking-wider">★</span>}
                                        </span>
                                        <span className="text-gray-400">
                                            {b.count} · € {b.amount.toFixed(2)}
                                        </span>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    )}
                </div>
            )}
        </section>
    );
}

function Stat({ label, value }: { label: string; value: string }) {
    return (
        <div className="bg-white/[0.03] border border-white/5 rounded-xl px-4 py-3">
            <p className="text-[10px] uppercase tracking-wider text-gray-500">{label}</p>
            <p className="text-lg font-light text-bottega-gold mt-0.5">{value}</p>
        </div>
    );
}

function SignalsBlock({ signals }: { signals: MarketTrendSignal[] }) {
    const { t } = useTranslation();

    return (
        <section className="animate-fade-up">
            <h3 className="text-sm font-medium text-white mb-3">{t('market_pulse.signals_title')}</h3>

            {signals.length === 0 ? (
                <div className="bg-white/[0.03] border border-white/5 rounded-xl px-4 py-6 text-center text-sm text-gray-400">
                    {t('market_pulse.no_signals')}
                </div>
            ) : (
                <div className="space-y-3">
                    {signals.map((s) => (
                        <SignalCard key={s.id} signal={s} />
                    ))}
                </div>
            )}
        </section>
    );
}

function SignalCard({ signal }: { signal: MarketTrendSignal }) {
    const { t } = useTranslation();
    const categoryLabel = CATEGORY_KEYS[signal.category] ? t(CATEGORY_KEYS[signal.category]) : signal.category;
    const directionLabel = DIRECTION_KEYS[signal.direction] ? t(DIRECTION_KEYS[signal.direction]) : signal.direction;
    const directionColor = signal.direction === 'rising'
        ? 'text-emerald-400'
        : signal.direction === 'declining'
            ? 'text-rose-400'
            : 'text-gray-400';
    const arrow = signal.direction === 'rising' ? '↗' : signal.direction === 'declining' ? '↘' : '→';

    return (
        <article className="bg-white/[0.03] border border-white/5 rounded-xl px-4 py-4 hover:border-bottega-gold/20 transition-all">
            <header className="flex items-start justify-between gap-3 mb-2">
                <div className="flex items-center gap-2 flex-wrap">
                    <span className="text-[10px] uppercase tracking-wider text-bottega-gold/80 px-2 py-0.5 bg-bottega-gold/10 rounded">
                        {categoryLabel}
                    </span>
                    {signal.medium && (
                        <span className="text-[10px] uppercase tracking-wider text-gray-400 px-2 py-0.5 bg-white/[0.04] rounded">
                            {signal.medium}
                        </span>
                    )}
                    {signal.region && (
                        <span className="text-[10px] uppercase tracking-wider text-gray-500">
                            {signal.region}
                        </span>
                    )}
                </div>
                <div className={`flex-shrink-0 ${directionColor} text-right`}>
                    <span className="text-lg leading-none">{arrow}</span>
                    <p className="text-[10px] uppercase tracking-wider mt-1">{directionLabel}</p>
                </div>
            </header>

            <p className="text-sm text-gray-200 leading-relaxed mb-2">{signal.insight}</p>

            {signal.actionable_advice && (
                <div className="mt-3 pt-3 border-t border-white/5">
                    <p className="text-[10px] uppercase tracking-wider text-gray-500 mb-1">{t('market_pulse.actionable_advice')}</p>
                    <p className="text-xs text-gray-300 leading-relaxed">{signal.actionable_advice}</p>
                </div>
            )}

            {signal.source && (
                <p className="mt-2 text-[10px] text-gray-500">
                    <span className="uppercase tracking-wider">{t('market_pulse.source')}</span>
                    <span className="ml-1.5">{signal.source}</span>
                </p>
            )}
        </article>
    );
}
