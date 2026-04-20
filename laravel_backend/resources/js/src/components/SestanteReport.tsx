/**
 * @package La Bottega — SestanteReport
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-20
 * @purpose Report Sestante — positioning artista vs comparabili (percentile + gap + top 3).
 */

import { useCallback, useEffect, useState } from 'react';
import {
    bottegaApi,
    type SestanteReport as SestanteReportType,
    type SestanteComparable,
    type SestanteOwnStats,
    type SestanteMetrics,
} from '@/api/bottegaApi';
import { useTranslation } from '@/hooks/useTranslation';

interface Props {
    open: boolean;
    onClose: () => void;
}

const CAREER_KEYS: Record<string, string> = {
    emerging: 'sestante.career_emerging',
    mid: 'sestante.career_mid',
    established: 'sestante.career_established',
};

export function SestanteReport({ open, onClose }: Props) {
    const { t } = useTranslation();
    const [report, setReport] = useState<SestanteReportType | null>(null);
    const [loading, setLoading] = useState(false);

    const run = useCallback(async () => {
        setLoading(true);
        try {
            const { data } = await bottegaApi.sestantePosition();
            setReport(data);
        } catch {
            setReport(null);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        if (open && !report && !loading) {
            run();
        }
    }, [open, report, loading, run]);

    if (!open) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-label={t('sestante.title')}>
            <div className="absolute inset-0 bg-black/60 backdrop-blur-sm" onClick={onClose} />

            <div
                className="relative w-full max-w-3xl max-h-[85vh] overflow-y-auto bg-bottega-navy border border-white/10 rounded-2xl shadow-2xl"
                style={{ scrollbarWidth: 'thin', scrollbarColor: 'rgba(212,175,55,0.15) transparent' }}
            >
                <div className="sticky top-0 z-10 bg-bottega-navy/95 backdrop-blur-sm border-b border-white/5 px-6 py-4 flex items-center justify-between gap-4">
                    <div className="min-w-0 flex-1">
                        <h2 className="text-lg font-light text-white">{t('sestante.title')}</h2>
                        <p className="text-xs text-gray-500 mt-0.5 truncate">{t('sestante.subtitle')}</p>
                    </div>
                    <button
                        onClick={onClose}
                        className="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 hover:text-white hover:bg-white/5 transition-colors"
                        aria-label={t('sestante.close')}
                    >
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div className="p-6 space-y-6">
                    {loading ? (
                        <LoadingState label={t('sestante.running')} />
                    ) : report ? (
                        <>
                            <Overview report={report} />
                            <OwnStatsBlock stats={report.own_stats} />
                            {report.has_data ? (
                                <>
                                    <MetricsBlock metrics={report.metrics} report={report} />
                                    <MedianBlock metrics={report.metrics} />
                                    {report.top_comparables.length > 0 && <TopBlock comparables={report.top_comparables} />}
                                </>
                            ) : (
                                <div className="bg-amber-400/5 border border-amber-400/20 rounded-xl px-4 py-6 text-center text-sm text-amber-400">
                                    {t('sestante.no_comparables')}
                                </div>
                            )}
                        </>
                    ) : (
                        <div className="text-center py-8">
                            <button
                                onClick={() => run()}
                                className="px-6 py-3 rounded-xl bg-bottega-gold/10 border border-bottega-gold/30 text-bottega-gold hover:bg-bottega-gold/20 transition-all"
                            >
                                {t('sestante.run')}
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

function Overview({ report }: { report: SestanteReportType }) {
    const { t } = useTranslation();
    const careerLabel = CAREER_KEYS[report.career_level] ? t(CAREER_KEYS[report.career_level]) : report.career_level;

    return (
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-2 animate-fade-up">
            <OverviewCell label={t('sestante.overview_comparables')} value={String(report.comparables_count)} />
            <OverviewCell label={t('sestante.overview_medium')} value={report.medium_primary ?? '—'} />
            <OverviewCell label={t('sestante.overview_career')} value={careerLabel} />
            <OverviewCell label={t('sestante.overview_completeness')} value={`${report.own_stats.profile_completeness}%`} />
        </div>
    );
}

function OverviewCell({ label, value }: { label: string; value: string }) {
    return (
        <div className="bg-white/[0.03] border border-white/5 rounded-xl px-3 py-2.5">
            <p className="text-[10px] uppercase tracking-wider text-gray-500">{label}</p>
            <p className="text-sm text-bottega-gold mt-0.5 truncate">{value}</p>
        </div>
    );
}

function OwnStatsBlock({ stats }: { stats: SestanteOwnStats }) {
    const { t } = useTranslation();
    return (
        <section className="animate-fade-up">
            <h3 className="text-sm font-medium text-white mb-3">{t('sestante.own_stats_title')}</h3>
            <div className="grid grid-cols-3 gap-2">
                <Stat label={t('sestante.own_avg_price')} value={formatPrice(stats.avg_price) ?? '—'} />
                <Stat label={t('sestante.own_egi_count')} value={String(stats.egi_count)} />
                <Stat label={t('sestante.own_sales_year')} value={String(stats.sales_count_year)} />
            </div>
        </section>
    );
}

function Stat({ label, value }: { label: string; value: string }) {
    return (
        <div className="bg-white/[0.03] border border-white/5 rounded-xl px-3 py-2.5">
            <p className="text-[10px] uppercase tracking-wider text-gray-500">{label}</p>
            <p className="text-sm text-gray-200 mt-0.5">{value}</p>
        </div>
    );
}

function MetricsBlock({ metrics, report }: { metrics: SestanteMetrics; report: SestanteReportType }) {
    const { t } = useTranslation();
    const gap = metrics.price_gap_pct;
    const gapLabel = gap === null
        ? null
        : gap > 2
            ? t('sestante.price_gap_above')
            : gap < -2
                ? t('sestante.price_gap_below')
                : t('sestante.price_gap_on');
    const gapColor = gap === null
        ? 'text-gray-400'
        : gap > 2
            ? 'text-emerald-400'
            : gap < -2
                ? 'text-rose-400'
                : 'text-gray-300';

    return (
        <section className="animate-fade-up">
            <h3 className="text-sm font-medium text-white mb-3" title={t('sestante.percentile_tooltip')}>{t('sestante.metrics_title')}</h3>
            <div className="space-y-2">
                <PercentileRow label={t('sestante.percentile_price')} value={metrics.percentile_price} />
                <PercentileRow label={t('sestante.percentile_visibility')} value={metrics.percentile_visibility} />
                <PercentileRow label={t('sestante.percentile_portfolio')} value={metrics.percentile_portfolio} />
            </div>
            {gap !== null && gapLabel && (
                <div className="mt-3 bg-white/[0.03] border border-white/5 rounded-xl px-4 py-2.5 flex items-center justify-between">
                    <span className={`text-xs ${gapColor}`}>{gapLabel}</span>
                    <span className={`text-sm ${gapColor}`}>
                        {gap > 0 ? '+' : ''}{gap}%
                    </span>
                </div>
            )}
            <p className="text-[10px] text-gray-500 mt-2 italic">
                {t('sestante.own_avg_price')}: {formatPrice(report.own_stats.avg_price) ?? '—'}
            </p>
        </section>
    );
}

function PercentileRow({ label, value }: { label: string; value: number | null }) {
    const pct = value ?? 0;
    const hasValue = value !== null;
    return (
        <div className="bg-white/[0.03] border border-white/5 rounded-xl px-4 py-3">
            <div className="flex items-center justify-between mb-1.5">
                <span className="text-xs uppercase tracking-wider text-gray-300">{label}</span>
                <span className="text-sm text-bottega-gold">{hasValue ? `${pct}` : '—'}</span>
            </div>
            <div className="h-1.5 bg-white/[0.04] rounded-full overflow-hidden">
                <div
                    className="h-full bg-gradient-to-r from-bottega-gold/40 to-bottega-gold rounded-full transition-all"
                    style={{ width: `${Math.max(2, pct)}%` }}
                />
            </div>
        </div>
    );
}

function MedianBlock({ metrics }: { metrics: SestanteMetrics }) {
    const { t } = useTranslation();
    return (
        <section className="animate-fade-up">
            <h3 className="text-sm font-medium text-white mb-3">{t('sestante.median_title')}</h3>
            <div className="grid grid-cols-3 gap-2">
                <Stat label={t('sestante.median_price')} value={formatPrice(metrics.median_price_comparables) ?? '—'} />
                <Stat label={t('sestante.median_sales')} value={metrics.median_sales_comparables !== null ? String(metrics.median_sales_comparables) : '—'} />
                <Stat label={t('sestante.median_egis')} value={metrics.median_egi_count_comparables !== null ? String(metrics.median_egi_count_comparables) : '—'} />
            </div>
        </section>
    );
}

function TopBlock({ comparables }: { comparables: SestanteComparable[] }) {
    const { t } = useTranslation();
    return (
        <section className="animate-fade-up">
            <h3 className="text-sm font-medium text-white mb-3">{t('sestante.top_title')}</h3>
            <ul className="space-y-2">
                {comparables.map((c, i) => (
                    <li key={i} className="bg-white/[0.03] border border-white/5 rounded-xl px-4 py-3">
                        <div className="flex items-center justify-between mb-2 gap-2">
                            <span className="text-sm text-white">{c.anonymous_label}</span>
                            <span className="text-[10px] uppercase tracking-wider text-gray-500">{c.medium ?? '—'}</span>
                        </div>
                        <div className="grid grid-cols-3 gap-2 text-xs">
                            <div>
                                <p className="text-[10px] uppercase tracking-wider text-gray-500">{t('sestante.comparable_avg_price')}</p>
                                <p className="text-bottega-gold mt-0.5">{formatPrice(c.avg_price)}</p>
                            </div>
                            <div>
                                <p className="text-[10px] uppercase tracking-wider text-gray-500">{t('sestante.comparable_egi_count')}</p>
                                <p className="text-gray-300 mt-0.5">{c.egi_count}</p>
                            </div>
                            <div>
                                <p className="text-[10px] uppercase tracking-wider text-gray-500">{t('sestante.comparable_sales')}</p>
                                <p className="text-gray-300 mt-0.5">{c.sales_count_year}</p>
                            </div>
                        </div>
                    </li>
                ))}
            </ul>
        </section>
    );
}

function formatPrice(v: number | null | undefined): string | null {
    if (v === null || v === undefined) return null;
    return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(v);
}
