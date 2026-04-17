/**
 * @package La Bottega — PriceAdvisorPanel
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Panel Price Advisor — regole, suggerimenti, incongruenze, edizioni limitate.
 */

import { useCallback, useEffect, useState } from 'react';
import {
    bottegaApi,
    type PriceAdvisorReport,
    type PriceAdvisorItem,
    type PriceAdvisorIncoherence,
    type PriceAdvisorEditionSuggestion,
} from '@/api/bottegaApi';
import { useTranslation } from '@/hooks/useTranslation';

interface Props {
    open: boolean;
    onClose: () => void;
}

export function PriceAdvisorPanel({ open, onClose }: Props) {
    const { t } = useTranslation();
    const [report, setReport] = useState<PriceAdvisorReport | null>(null);
    const [loading, setLoading] = useState(false);

    const run = useCallback(async () => {
        setLoading(true);
        try {
            const { data } = await bottegaApi.priceAdvisorAnalyze();
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
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-label={t('price_advisor.title')}>
            <div className="absolute inset-0 bg-black/60 backdrop-blur-sm" onClick={onClose} />

            <div
                className="relative w-full max-w-3xl max-h-[85vh] overflow-y-auto bg-bottega-navy border border-white/10 rounded-2xl shadow-2xl"
                style={{ scrollbarWidth: 'thin', scrollbarColor: 'rgba(212,175,55,0.15) transparent' }}
            >
                <div className="sticky top-0 z-10 bg-bottega-navy/95 backdrop-blur-sm border-b border-white/5 px-6 py-4 flex items-center justify-between gap-4">
                    <div className="min-w-0 flex-1">
                        <h2 className="text-lg font-light text-white">{t('price_advisor.title')}</h2>
                        <p className="text-xs text-gray-500 mt-0.5 truncate">{t('price_advisor.subtitle')}</p>
                    </div>
                    <button
                        onClick={onClose}
                        className="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 hover:text-white hover:bg-white/5 transition-colors"
                        aria-label={t('price_advisor.close')}
                    >
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div className="p-6 space-y-6">
                    {loading ? (
                        <LoadingState label={t('price_advisor.running')} />
                    ) : report ? (
                        <>
                            <Overview report={report} />
                            <RulesBlock report={report} />
                            {report.items.length > 0 && <ItemsBlock items={report.items} />}
                            <IncoherencesBlock incoherences={report.incoherences} />
                            {report.edition_suggestions.length > 0 && <EditionsBlock suggestions={report.edition_suggestions} />}
                        </>
                    ) : (
                        <div className="text-center py-8">
                            <button
                                onClick={() => run()}
                                className="px-6 py-3 rounded-xl bg-bottega-gold/10 border border-bottega-gold/30 text-bottega-gold hover:bg-bottega-gold/20 transition-all"
                            >
                                {t('price_advisor.run')}
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

function Overview({ report }: { report: PriceAdvisorReport }) {
    const { t } = useTranslation();
    const withoutPrice = report.items.filter(i => i.current_price === null).length;

    return (
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-2 animate-fade-up">
            <OverviewCell label={t('price_advisor.overview_total')} value={report.items.length} />
            <OverviewCell label={t('price_advisor.overview_analyzed')} value={report.analyzed_count} />
            <OverviewCell label={t('price_advisor.overview_without_price')} value={withoutPrice} tone={withoutPrice > 0 ? 'amber' : 'neutral'} />
            <OverviewCell label={t('price_advisor.overview_incoherent')} value={report.incoherences.length} tone={report.incoherences.length > 0 ? 'rose' : 'neutral'} />
        </div>
    );
}

function OverviewCell({ label, value, tone = 'neutral' }: { label: string; value: number; tone?: 'neutral' | 'amber' | 'rose' }) {
    const toneClass = tone === 'amber'
        ? 'text-amber-400'
        : tone === 'rose'
            ? 'text-rose-400'
            : 'text-bottega-gold';
    return (
        <div className="bg-white/[0.03] border border-white/5 rounded-xl px-3 py-2.5">
            <p className="text-[10px] uppercase tracking-wider text-gray-500">{label}</p>
            <p className={`text-lg font-light mt-0.5 ${toneClass}`}>{value}</p>
        </div>
    );
}

function RulesBlock({ report }: { report: PriceAdvisorReport }) {
    const { t } = useTranslation();
    const rules = [
        { key: 'floor', label: t('price_advisor.rule_floor'), text: report.rules.price_floor },
        { key: 'editions', label: t('price_advisor.rule_editions'), text: report.rules.edition_ranges },
        { key: 'coherence', label: t('price_advisor.rule_coherence'), text: report.rules.coherence },
    ];
    return (
        <section className="animate-fade-up">
            <h3 className="text-sm font-medium text-white mb-3">{t('price_advisor.rules_title')}</h3>
            <ul className="space-y-2">
                {rules.map(r => (
                    <li key={r.key} className="bg-white/[0.03] border border-white/5 rounded-xl px-4 py-3">
                        <p className="text-xs uppercase tracking-wider text-bottega-gold/70">{r.label}</p>
                        <p className="text-xs text-gray-300 mt-1 leading-relaxed">{r.text}</p>
                    </li>
                ))}
            </ul>
        </section>
    );
}

function ItemsBlock({ items }: { items: PriceAdvisorItem[] }) {
    const { t } = useTranslation();
    return (
        <section className="animate-fade-up">
            <h3 className="text-sm font-medium text-white mb-3">{t('price_advisor.items_title')}</h3>
            <ul className="space-y-2">
                {items.map(item => (
                    <li key={item.egi_id} className="bg-white/[0.03] border border-white/5 rounded-xl px-4 py-3">
                        <div className="flex items-center justify-between mb-2 gap-2">
                            <span className="text-sm text-white truncate">{item.title ?? `#${item.egi_id}`}</span>
                            {item.medium && (
                                <span className="text-[10px] uppercase tracking-wider text-gray-500 flex-shrink-0">{item.medium}</span>
                            )}
                        </div>
                        <div className="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <p className="text-[10px] uppercase tracking-wider text-gray-500">{t('price_advisor.current_price')}</p>
                                <p className="text-gray-300 mt-0.5">{formatPrice(item.current_price) ?? t('price_advisor.no_price_set')}</p>
                            </div>
                            <div>
                                <p className="text-[10px] uppercase tracking-wider text-gray-500">{t('price_advisor.suggested_price')}</p>
                                <p className="text-bottega-gold mt-0.5">{formatPrice(item.suggested_price) ?? '—'}</p>
                            </div>
                        </div>
                        {item.rule_floor_applied && (
                            <p className="text-[10px] text-amber-400 mt-2">{t('price_advisor.kept_current')}</p>
                        )}
                        {item.npe_confidence !== null && (
                            <p className="text-[10px] text-gray-500 mt-1">
                                {t('price_advisor.confidence')}: {Math.round(item.npe_confidence * 100)}%
                            </p>
                        )}
                    </li>
                ))}
            </ul>
        </section>
    );
}

function IncoherencesBlock({ incoherences }: { incoherences: PriceAdvisorIncoherence[] }) {
    const { t } = useTranslation();
    if (incoherences.length === 0) {
        return (
            <section className="animate-fade-up">
                <h3 className="text-sm font-medium text-white mb-3">{t('price_advisor.incoherences_title')}</h3>
                <div className="bg-emerald-400/5 border border-emerald-400/20 rounded-xl px-4 py-3 text-xs text-emerald-400">
                    {t('price_advisor.incoherences_empty')}
                </div>
            </section>
        );
    }
    return (
        <section className="animate-fade-up">
            <h3 className="text-sm font-medium text-white mb-3">{t('price_advisor.incoherences_title')}</h3>
            <ul className="space-y-2">
                {incoherences.map((inc, i) => (
                    <li key={i} className="bg-rose-400/5 border border-rose-400/20 rounded-xl px-4 py-3">
                        <div className="flex items-center justify-between mb-1">
                            <span className="text-xs uppercase tracking-wider text-rose-400">{inc.medium}</span>
                            <span className="text-xs text-rose-400">{t('price_advisor.incoherence_gap')}: {inc.gap_pct}%</span>
                        </div>
                        <p className="text-[11px] text-gray-400">
                            {formatPrice(inc.min_price)} → {formatPrice(inc.max_price)} ({inc.items_count})
                        </p>
                    </li>
                ))}
            </ul>
        </section>
    );
}

function EditionsBlock({ suggestions }: { suggestions: PriceAdvisorEditionSuggestion[] }) {
    const { t } = useTranslation();
    const keyFor = (size: number) => size === 10 ? 'price_advisor.edition_10' : size === 25 ? 'price_advisor.edition_25' : 'price_advisor.edition_50';
    return (
        <section className="animate-fade-up">
            <h3 className="text-sm font-medium text-white mb-3">{t('price_advisor.editions_title')}</h3>
            <ul className="space-y-2">
                {suggestions.map(sug => (
                    <li key={sug.egi_id} className="bg-white/[0.03] border border-white/5 rounded-xl px-4 py-3">
                        <div className="flex items-center justify-between mb-2 gap-2">
                            <span className="text-sm text-white truncate">{sug.title ?? `#${sug.egi_id}`}</span>
                            <span className="text-xs text-bottega-gold flex-shrink-0">{formatPrice(sug.base_price)}</span>
                        </div>
                        <div className="grid grid-cols-3 gap-2">
                            {sug.ranges.map(r => (
                                <div key={r.edition_size} className="bg-black/20 border border-white/5 rounded-lg px-2 py-1.5">
                                    <p className="text-[10px] uppercase tracking-wider text-gray-500">{t(keyFor(r.edition_size))}</p>
                                    <p className="text-[11px] text-gray-300 mt-0.5">{formatPrice(r.min_price)} – {formatPrice(r.max_price)}</p>
                                </div>
                            ))}
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
