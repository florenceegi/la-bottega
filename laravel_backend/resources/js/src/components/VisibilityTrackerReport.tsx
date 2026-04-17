/**
 * @package La Bottega — VisibilityTrackerReport
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Report Visibility Tracker — funnel acquisizione + breakdown eventi artista.
 */

import { useCallback, useEffect, useState } from 'react';
import {
    bottegaApi,
    type VisibilityReport as VisibilityReportType,
    type VisibilityFunnelStage,
    type VisibilityEventCount,
} from '@/api/bottegaApi';
import { useTranslation } from '@/hooks/useTranslation';

interface Props {
    open: boolean;
    onClose: () => void;
}

const STAGE_KEYS: Record<string, string> = {
    awareness: 'visibility.stage_awareness',
    interest: 'visibility.stage_interest',
    consideration: 'visibility.stage_consideration',
    conversion: 'visibility.stage_conversion',
};

const EVENT_KEYS: Record<string, string> = {
    profile_view: 'visibility.event_profile_view',
    egi_view: 'visibility.event_egi_view',
    collection_view: 'visibility.event_collection_view',
    bio_read: 'visibility.event_bio_read',
    egi_favorite: 'visibility.event_egi_favorite',
    coa_verify: 'visibility.event_coa_verify',
    egi_purchase: 'visibility.event_egi_purchase',
};

const WINDOWS = [7, 14, 30];

export function VisibilityTrackerReport({ open, onClose }: Props) {
    const { t } = useTranslation();
    const [report, setReport] = useState<VisibilityReportType | null>(null);
    const [loading, setLoading] = useState(false);
    const [days, setDays] = useState(7);

    const run = useCallback(async (windowDays: number) => {
        setLoading(true);
        try {
            const { data } = await bottegaApi.visibilityReport(windowDays);
            setReport(data);
        } catch {
            setReport(null);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        if (open && !report && !loading) {
            run(days);
        }
    }, [open, report, loading, run, days]);

    const changeWindow = (newDays: number) => {
        setDays(newDays);
        setReport(null);
        run(newDays);
    };

    if (!open) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-label={t('visibility.title')}>
            <div className="absolute inset-0 bg-black/60 backdrop-blur-sm" onClick={onClose} />

            <div
                className="relative w-full max-w-3xl max-h-[85vh] overflow-y-auto bg-bottega-navy border border-white/10 rounded-2xl shadow-2xl"
                style={{ scrollbarWidth: 'thin', scrollbarColor: 'rgba(212,175,55,0.15) transparent' }}
            >
                <div className="sticky top-0 z-10 bg-bottega-navy/95 backdrop-blur-sm border-b border-white/5 px-6 py-4 flex items-center justify-between gap-4">
                    <div className="min-w-0 flex-1">
                        <h2 className="text-lg font-light text-white">{t('visibility.title')}</h2>
                        <p className="text-xs text-gray-500 mt-0.5 truncate">{t('visibility.subtitle')}</p>
                    </div>
                    <div className="flex items-center gap-1">
                        {WINDOWS.map(w => (
                            <button
                                key={w}
                                onClick={() => changeWindow(w)}
                                className={`text-[10px] uppercase tracking-wider px-2 py-1 rounded border transition-all ${
                                    days === w
                                        ? 'border-bottega-gold/40 bg-bottega-gold/10 text-bottega-gold'
                                        : 'border-white/5 text-gray-500 hover:text-gray-300 hover:border-white/10'
                                }`}
                            >
                                {w}{t('visibility.days').charAt(0)}
                            </button>
                        ))}
                    </div>
                    <button
                        onClick={onClose}
                        className="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 hover:text-white hover:bg-white/5 transition-colors"
                        aria-label={t('visibility.close')}
                    >
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div className="p-6 space-y-6">
                    {loading ? (
                        <LoadingState label={t('visibility.running')} />
                    ) : report ? (
                        <>
                            <Overview report={report} />
                            {!report.has_data ? (
                                <div className="bg-amber-400/5 border border-amber-400/20 rounded-xl px-4 py-6 text-center text-sm text-amber-400">
                                    {t('visibility.no_data')}
                                </div>
                            ) : (
                                <>
                                    <FunnelBlock funnel={report.funnel} />
                                    <BreakdownBlock events={report.events_breakdown} />
                                    <SourcesBlock report={report} />
                                </>
                            )}
                        </>
                    ) : (
                        <div className="text-center py-8">
                            <button
                                onClick={() => run(days)}
                                className="px-6 py-3 rounded-xl bg-bottega-gold/10 border border-bottega-gold/30 text-bottega-gold hover:bg-bottega-gold/20 transition-all"
                            >
                                {t('visibility.run')}
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

function Overview({ report }: { report: VisibilityReportType }) {
    const { t } = useTranslation();
    const deltaColor = report.delta_pct === null
        ? 'text-gray-400'
        : report.delta_pct > 0
            ? 'text-emerald-400'
            : report.delta_pct < 0
                ? 'text-rose-400'
                : 'text-gray-400';
    const deltaStr = report.delta_pct === null
        ? '—'
        : `${report.delta_pct > 0 ? '+' : ''}${report.delta_pct}%`;

    return (
        <div className="flex items-center justify-between bg-white/[0.03] border border-white/5 rounded-xl px-4 py-3 animate-fade-up">
            <div>
                <p className="text-[10px] uppercase tracking-wider text-gray-500">{t('visibility.total_events')}</p>
                <p className="text-lg font-light text-bottega-gold mt-0.5">{report.total_events}</p>
            </div>
            <div className={`text-right ${deltaColor}`}>
                <p className="text-[10px] uppercase tracking-wider text-gray-500">{t('visibility.vs_prior')}</p>
                <p className="text-sm mt-0.5">{deltaStr}</p>
                <p className="text-[10px] text-gray-500">({report.total_events_prior})</p>
            </div>
        </div>
    );
}

function FunnelBlock({ funnel }: { funnel: VisibilityFunnelStage[] }) {
    const { t } = useTranslation();
    const max = Math.max(1, ...funnel.map(s => s.count));

    return (
        <section className="animate-fade-up">
            <h3 className="text-sm font-medium text-white mb-3">{t('visibility.funnel_title')}</h3>
            <ol className="space-y-2">
                {funnel.map((stage) => {
                    const stageLabel = STAGE_KEYS[stage.stage] ? t(STAGE_KEYS[stage.stage]) : stage.stage;
                    const pct = Math.max(4, (stage.count / max) * 100);
                    const deltaColor = stage.delta_pct === null
                        ? 'text-gray-500'
                        : stage.delta_pct > 0
                            ? 'text-emerald-400'
                            : stage.delta_pct < 0
                                ? 'text-rose-400'
                                : 'text-gray-500';
                    const deltaStr = stage.delta_pct === null
                        ? ''
                        : ` (${stage.delta_pct > 0 ? '+' : ''}${stage.delta_pct}%)`;
                    return (
                        <li key={stage.stage} className="bg-white/[0.03] border border-white/5 rounded-xl px-4 py-3">
                            <div className="flex items-center justify-between mb-2">
                                <span className="text-xs uppercase tracking-wider text-gray-300">{stageLabel}</span>
                                <span className="text-sm text-bottega-gold">
                                    {stage.count}
                                    <span className={`ml-1.5 text-[10px] ${deltaColor}`}>{deltaStr}</span>
                                </span>
                            </div>
                            <div className="h-1.5 bg-white/[0.04] rounded-full overflow-hidden">
                                <div
                                    className="h-full bg-gradient-to-r from-bottega-gold/40 to-bottega-gold rounded-full transition-all"
                                    style={{ width: `${pct}%` }}
                                />
                            </div>
                        </li>
                    );
                })}
            </ol>
        </section>
    );
}

function BreakdownBlock({ events }: { events: VisibilityEventCount[] }) {
    const { t } = useTranslation();

    if (events.length === 0) return null;

    return (
        <section className="animate-fade-up">
            <h3 className="text-sm font-medium text-white mb-3">{t('visibility.breakdown_title')}</h3>
            <ul className="grid grid-cols-2 gap-2">
                {events.map((e) => {
                    const label = EVENT_KEYS[e.event_type] ? t(EVENT_KEYS[e.event_type]) : e.event_type;
                    return (
                        <li key={e.event_type} className="flex items-center justify-between bg-white/[0.03] border border-white/5 rounded-lg px-3 py-2 text-xs">
                            <span className="text-gray-300 truncate">{label}</span>
                            <span className="text-bottega-gold flex-shrink-0 ml-2">{e.count}</span>
                        </li>
                    );
                })}
            </ul>
        </section>
    );
}

function SourcesBlock({ report }: { report: VisibilityReportType }) {
    const { t } = useTranslation();
    const hasReferrers = report.top_referrers.length > 0;
    const hasCountries = report.top_countries.length > 0;

    if (!hasReferrers && !hasCountries) return null;

    return (
        <section className="grid grid-cols-1 sm:grid-cols-2 gap-4 animate-fade-up">
            {hasReferrers && (
                <div>
                    <h4 className="text-xs uppercase tracking-wider text-gray-500 mb-2">{t('visibility.top_referrers')}</h4>
                    <ul className="space-y-1">
                        {report.top_referrers.map((r, i) => (
                            <li key={i} className="flex items-center justify-between bg-white/[0.03] border border-white/5 rounded-lg px-3 py-1.5 text-xs">
                                <span className="text-gray-300 truncate">{r.referrer}</span>
                                <span className="text-gray-400 flex-shrink-0 ml-2">{r.count}</span>
                            </li>
                        ))}
                    </ul>
                </div>
            )}
            {hasCountries && (
                <div>
                    <h4 className="text-xs uppercase tracking-wider text-gray-500 mb-2">{t('visibility.top_countries')}</h4>
                    <ul className="space-y-1">
                        {report.top_countries.map((c, i) => (
                            <li key={i} className="flex items-center justify-between bg-white/[0.03] border border-white/5 rounded-lg px-3 py-1.5 text-xs">
                                <span className="text-gray-300">{c.country}</span>
                                <span className="text-gray-400 flex-shrink-0 ml-2">{c.count}</span>
                            </li>
                        ))}
                    </ul>
                </div>
            )}
        </section>
    );
}
