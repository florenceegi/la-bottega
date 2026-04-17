/**
 * @package La Bottega — BinocoloReport
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Report Binocolo — opportunita esterne matching con profilo artista.
 */

import { useCallback, useEffect, useState } from 'react';
import { bottegaApi, type BinocoloReport as BinocoloReportType } from '@/api/bottegaApi';
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

const TYPE_KEYS: Record<string, string> = {
    call: 'binocolo.type_call',
    residency: 'binocolo.type_residency',
    fair: 'binocolo.type_fair',
    prize: 'binocolo.type_prize',
    event: 'binocolo.type_event',
};

export function BinocoloReport({ open, onClose }: Props) {
    const { t } = useTranslation();
    const [report, setReport] = useState<BinocoloReportType | null>(null);
    const [loading, setLoading] = useState(false);

    const runMatch = useCallback(async () => {
        setLoading(true);
        try {
            const { data } = await bottegaApi.binocoloMatch();
            setReport(data);
        } catch {
            setReport(null);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        if (open && !report && !loading) {
            runMatch();
        }
    }, [open, report, loading, runMatch]);

    if (!open) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-label={t('binocolo.title')}>
            <div className="absolute inset-0 bg-black/60 backdrop-blur-sm" onClick={onClose} />

            <div
                className="relative w-full max-w-3xl max-h-[85vh] overflow-y-auto bg-bottega-navy border border-white/10 rounded-2xl shadow-2xl"
                style={{ scrollbarWidth: 'thin', scrollbarColor: 'rgba(212,175,55,0.15) transparent' }}
            >
                <div className="sticky top-0 z-10 bg-bottega-navy/95 backdrop-blur-sm border-b border-white/5 px-6 py-4 flex items-center justify-between">
                    <div>
                        <h2 className="text-lg font-light text-white">{t('binocolo.title')}</h2>
                        <p className="text-xs text-gray-500 mt-0.5">{t('binocolo.subtitle')}</p>
                    </div>
                    <button
                        onClick={onClose}
                        className="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 hover:text-white hover:bg-white/5 transition-colors"
                        aria-label={t('binocolo.close')}
                    >
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div className="p-6 space-y-6">
                    {loading ? (
                        <LoadingState label={t('binocolo.running')} />
                    ) : report ? (
                        <>
                            <Overview report={report} />
                            <ResultsList report={report} />
                        </>
                    ) : (
                        <div className="text-center py-8">
                            <button
                                onClick={runMatch}
                                className="px-6 py-3 rounded-xl bg-bottega-gold/10 border border-bottega-gold/30 text-bottega-gold hover:bg-bottega-gold/20 transition-all"
                            >
                                {t('binocolo.run')}
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

function Overview({ report }: { report: BinocoloReportType }) {
    const { t } = useTranslation();
    const careerLabel = t(CAREER_KEYS[report.career_level] ?? report.career_level);

    return (
        <div className="flex items-center justify-between bg-white/[0.03] border border-white/5 rounded-xl px-4 py-3 animate-fade-up">
            <div>
                <p className="text-[10px] uppercase tracking-wider text-gray-500">{t('binocolo.career_level')}</p>
                <p className="text-sm text-white mt-0.5">{careerLabel}</p>
            </div>
            <div className="text-right">
                <p className="text-[10px] uppercase tracking-wider text-gray-500">{t('binocolo.matched_count')}</p>
                <p className="text-lg font-light text-bottega-gold mt-0.5">
                    {report.matched_count}
                    <span className="text-sm text-gray-500 ml-1">/ {report.total_opportunities}</span>
                </p>
            </div>
        </div>
    );
}

function ResultsList({ report }: { report: BinocoloReportType }) {
    const { t } = useTranslation();

    if (report.results.length === 0) {
        return (
            <div className="bg-amber-400/5 border border-amber-400/20 rounded-xl px-4 py-6 text-center text-sm text-amber-400">
                {t('binocolo.no_results')}
            </div>
        );
    }

    return (
        <div className="space-y-3">
            {report.results.map((item, i) => (
                <OpportunityCard key={i} item={item} />
            ))}
        </div>
    );
}

function OpportunityCard({ item }: { item: BinocoloReportType['results'][number] }) {
    const { t } = useTranslation();
    const opp = item.opportunity;
    const typeLabel = TYPE_KEYS[opp.type] ? t(TYPE_KEYS[opp.type]) : opp.type;
    const scoreColor = item.score >= 70 ? 'text-emerald-400' : item.score >= 50 ? 'text-amber-400' : 'text-gray-400';

    return (
        <article className="bg-white/[0.03] border border-white/5 rounded-xl px-4 py-4 hover:border-bottega-gold/20 transition-all animate-fade-up">
            <header className="flex items-start justify-between gap-3 mb-2">
                <div className="min-w-0 flex-1">
                    <div className="flex items-center gap-2 mb-1">
                        <span className="text-[10px] uppercase tracking-wider text-bottega-gold/80 px-2 py-0.5 bg-bottega-gold/10 rounded">
                            {typeLabel}
                        </span>
                        {opp.country && (
                            <span className="text-[10px] uppercase tracking-wider text-gray-500">
                                {opp.country}
                            </span>
                        )}
                        {opp.verified && (
                            <svg className="w-3 h-3 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" strokeWidth={1.5} stroke="currentColor" fill="none" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        )}
                    </div>
                    <h3 className="text-sm font-medium text-white truncate">{opp.title}</h3>
                </div>
                <div className={`text-right flex-shrink-0 ${scoreColor}`}>
                    <p className="text-[10px] uppercase tracking-wider text-gray-500">{t('binocolo.score_label')}</p>
                    <p className="text-lg font-light">{item.score}</p>
                </div>
            </header>

            {opp.description && (
                <p className="text-xs text-gray-400 leading-relaxed mb-3 line-clamp-3">{opp.description}</p>
            )}

            {item.match_reasons.length > 0 && (
                <div className="mb-3">
                    <p className="text-[10px] uppercase tracking-wider text-gray-500 mb-1">{t('binocolo.match_reasons')}</p>
                    <ul className="flex flex-wrap gap-1.5">
                        {item.match_reasons.map((reason, idx) => (
                            <li key={idx} className="text-[11px] text-gray-300 px-2 py-0.5 bg-white/[0.04] border border-white/5 rounded">
                                {reason}
                            </li>
                        ))}
                    </ul>
                </div>
            )}

            <footer className="flex items-center justify-between gap-3 pt-2 border-t border-white/5">
                <div className="text-[11px] text-gray-500">
                    {opp.deadline ? (
                        <>
                            <span className="uppercase tracking-wider">{t('binocolo.deadline')}</span>
                            <span className="ml-1.5 text-gray-300">{opp.deadline}</span>
                            {opp.days_remaining !== null && opp.days_remaining !== undefined && (
                                <span className="ml-1.5 text-bottega-gold">
                                    ({opp.days_remaining} {t('binocolo.days_remaining')})
                                </span>
                            )}
                        </>
                    ) : (
                        <span className="text-gray-400">{t('binocolo.rolling')}</span>
                    )}
                </div>
                {opp.url && (
                    <a
                        href={opp.url}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="inline-flex items-center gap-1.5 text-xs text-bottega-gold hover:text-bottega-gold-light transition-colors"
                    >
                        {t('binocolo.visit_url')}
                        <svg className="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                    </a>
                )}
            </footer>
        </article>
    );
}
