/**
 * @package La Bottega — MicroscopioReport
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-14
 * @purpose Report Microscopio — score ring, findings prioritizzati, azioni fix NPE
 */

import { useCallback, useEffect, useState } from 'react';
import { bottegaApi, type MicroscopioReport as MicroscopioReportType } from '@/api/bottegaApi';
import { useTranslation } from '@/hooks/useTranslation';
import DOMPurify from 'dompurify';

interface Props {
    open: boolean;
    onClose: () => void;
}

const CATEGORY_KEYS: Record<string, string> = {
    identity: 'microscopio.identity',
    completeness: 'microscopio.completeness',
    coherence: 'microscopio.coherence',
    visibility: 'microscopio.visibility',
};

const PRIORITY_COLORS: Record<string, string> = {
    critical: 'text-red-400 bg-red-400/10 border-red-400/20',
    high: 'text-amber-400 bg-amber-400/10 border-amber-400/20',
    medium: 'text-blue-400 bg-blue-400/10 border-blue-400/20',
    low: 'text-gray-400 bg-gray-400/10 border-gray-400/20',
};

export function MicroscopioReport({ open, onClose }: Props) {
    const { t } = useTranslation();
    const [report, setReport] = useState<MicroscopioReportType | null>(null);
    const [loading, setLoading] = useState(false);
    const [fixing, setFixing] = useState<string | null>(null);
    const [fixMessage, setFixMessage] = useState<string | null>(null);

    const runAnalysis = useCallback(async () => {
        setLoading(true);
        setFixMessage(null);
        try {
            const { data } = await bottegaApi.microscopioRun();
            setReport(data);
        } catch {
            setReport(null);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        if (open && !report && !loading) {
            runAnalysis();
        }
    }, [open, report, loading, runAnalysis]);

    const handleFixDescriptions = useCallback(async () => {
        setFixing('descriptions');
        try {
            const { data } = await bottegaApi.microscopioFixDescriptions();
            setFixMessage(data.message ?? data.error ?? '');
        } finally {
            setFixing(null);
        }
    }, []);

    const handleFixPricing = useCallback(async (egiId: number) => {
        setFixing('pricing');
        try {
            const { data } = await bottegaApi.microscopioFixPricing(egiId);
            setFixMessage(data.message ?? data.error ?? '');
        } finally {
            setFixing(null);
        }
    }, []);

    const handleFixCoherence = useCallback(async (collectionId: number) => {
        setFixing('coherence');
        try {
            const { data } = await bottegaApi.microscopioFixCoherence(collectionId);
            setFixMessage(data.message ?? data.error ?? '');
        } finally {
            setFixing(null);
        }
    }, []);

    if (!open) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-label={t('microscopio.title')}>
            {/* Backdrop */}
            <div className="absolute inset-0 bg-black/60 backdrop-blur-sm" onClick={onClose} />

            {/* Panel */}
            <div className="relative w-full max-w-2xl max-h-[85vh] overflow-y-auto bg-bottega-navy border border-white/10 rounded-2xl shadow-2xl" style={{ scrollbarWidth: 'thin', scrollbarColor: 'rgba(212,175,55,0.15) transparent' }}>
                {/* Header */}
                <div className="sticky top-0 z-10 bg-bottega-navy/95 backdrop-blur-sm border-b border-white/5 px-6 py-4 flex items-center justify-between">
                    <div>
                        <h2 className="text-lg font-light text-white">{t('microscopio.title')}</h2>
                        <p className="text-xs text-gray-500 mt-0.5">{t('microscopio.subtitle')}</p>
                    </div>
                    <button onClick={onClose} className="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 hover:text-white hover:bg-white/5 transition-colors" aria-label={t('microscopio.close')}>
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div className="p-6 space-y-6">
                    {loading ? (
                        <LoadingState label={t('microscopio.running')} />
                    ) : report ? (
                        <>
                            <ScoreOverview report={report} />
                            <CategoryScores scores={report.scores} />
                            <Findings findings={report.findings} />
                            <Recommendations
                                recommendations={report.recommendations}
                                weakDescriptions={report.weak_descriptions_count}
                                fixing={fixing}
                                onFixDescriptions={handleFixDescriptions}
                                onFixPricing={handleFixPricing}
                                onFixCoherence={handleFixCoherence}
                            />
                            {fixMessage && (
                                <div className="bg-bottega-gold/10 border border-bottega-gold/20 rounded-xl px-4 py-3 text-sm text-bottega-gold animate-fade-up">
                                    {fixMessage}
                                </div>
                            )}
                        </>
                    ) : (
                        <div className="text-center py-8">
                            <button onClick={runAnalysis} className="px-6 py-3 rounded-xl bg-bottega-gold/10 border border-bottega-gold/30 text-bottega-gold hover:bg-bottega-gold/20 transition-all">
                                {t('microscopio.run')}
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

function ScoreOverview({ report }: { report: MicroscopioReportType }) {
    const { t } = useTranslation();
    const score = report.total_score;
    const circumference = 2 * Math.PI * 40;
    const offset = circumference - (score / 100) * circumference;
    const color = score >= 70 ? '#34d399' : score >= 40 ? '#fbbf24' : '#f87171';

    return (
        <div className="flex items-center gap-6 animate-fade-up">
            <div className="relative w-24 h-24 flex-shrink-0">
                <svg className="w-24 h-24 -rotate-90" viewBox="0 0 96 96">
                    <circle cx="48" cy="48" r="40" fill="none" stroke="rgba(255,255,255,0.05)" strokeWidth="6" />
                    <circle cx="48" cy="48" r="40" fill="none" stroke={color} strokeWidth="6" strokeLinecap="round" strokeDasharray={circumference} strokeDashoffset={offset} className="transition-all duration-1000" />
                </svg>
                <div className="absolute inset-0 flex items-center justify-center">
                    <span className="text-2xl font-light text-white">{score}</span>
                </div>
            </div>
            <div>
                <p className="text-sm text-gray-500">{t('microscopio.score_label')}</p>
                <p className="text-xl font-light text-white">{score}/100</p>
                <p className="text-xs text-gray-600 mt-1">
                    {report.findings_count} {t('microscopio.findings').toLowerCase()}
                </p>
            </div>
        </div>
    );
}

function CategoryScores({ scores }: { scores: MicroscopioReportType['scores'] }) {
    const { t } = useTranslation();

    return (
        <div className="grid grid-cols-2 gap-3 animate-fade-up">
            {Object.entries(scores).map(([key, value]) => (
                <div key={key} className="bg-white/[0.03] border border-white/5 rounded-xl px-4 py-3">
                    <p className="text-[10px] uppercase tracking-wider text-gray-500">{t(CATEGORY_KEYS[key] ?? key)}</p>
                    <div className="flex items-end gap-2 mt-1">
                        <span className="text-lg font-light text-white">{value}</span>
                        <span className="text-xs text-gray-600 mb-0.5">/25</span>
                    </div>
                    <div className="mt-2 h-1 bg-white/5 rounded-full overflow-hidden">
                        <div className="h-full bg-bottega-gold/60 rounded-full transition-all duration-700" style={{ width: `${(value / 25) * 100}%` }} />
                    </div>
                </div>
            ))}
        </div>
    );
}

function Findings({ findings }: { findings: MicroscopioReportType['findings'] }) {
    const { t } = useTranslation();

    if (findings.length === 0) {
        return (
            <div className="bg-emerald-400/5 border border-emerald-400/20 rounded-xl px-4 py-3 text-sm text-emerald-400">
                {t('microscopio.no_findings')}
            </div>
        );
    }

    return (
        <div className="space-y-2">
            <h3 className="text-xs uppercase tracking-wider text-gray-500">{t('microscopio.findings')}</h3>
            {findings.map((f, i) => (
                <div key={i} className={`flex items-start gap-3 rounded-xl border px-4 py-3 ${PRIORITY_COLORS[f.priority] ?? PRIORITY_COLORS.low}`}>
                    <span className="text-[10px] uppercase tracking-wider font-medium mt-0.5 flex-shrink-0">
                        {t(`microscopio.priority_${f.priority}`)}
                    </span>
                    <p className="text-sm text-gray-300" dangerouslySetInnerHTML={{ __html: DOMPurify.sanitize(f.message) }} />
                </div>
            ))}
        </div>
    );
}

function Recommendations({
    recommendations,
    weakDescriptions,
    fixing,
    onFixDescriptions,
    onFixPricing,
    onFixCoherence,
}: {
    recommendations: MicroscopioReportType['recommendations'];
    weakDescriptions: number;
    fixing: string | null;
    onFixDescriptions: () => void;
    onFixPricing: (egiId: number) => void;
    onFixCoherence: (collectionId: number) => void;
}) {
    const { t } = useTranslation();

    if (recommendations.length === 0) return null;

    return (
        <div className="space-y-3">
            <h3 className="text-xs uppercase tracking-wider text-gray-500">{t('microscopio.recommendations')}</h3>

            {recommendations.map((rec, i) => (
                <div key={i} className="bg-white/[0.03] border border-white/5 rounded-xl px-4 py-3">
                    <p className="text-sm text-gray-300 mb-2">{rec.message}</p>
                    {rec.action_url && rec.action_label && (
                        <a
                            href={rec.action_url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-1.5 text-xs text-bottega-gold hover:text-bottega-gold-light transition-colors"
                        >
                            {rec.action_label}
                            <svg className="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </a>
                    )}
                </div>
            ))}

            {/* NPE Fix Buttons */}
            <div className="flex flex-wrap gap-2 pt-2">
                {weakDescriptions > 0 && (
                    <FixButton
                        label={`${t('microscopio.fix_descriptions')} (${weakDescriptions})`}
                        loading={fixing === 'descriptions'}
                        loadingLabel={t('microscopio.fixing')}
                        onClick={onFixDescriptions}
                    />
                )}
                {/* Fix pricing e coherence richiedono ID specifici dal report — disabilitati fino a implementazione */}
            </div>
        </div>
    );
}

function FixButton({ label, loading, loadingLabel, onClick }: {
    label: string;
    loading: boolean;
    loadingLabel: string;
    onClick: () => void;
}) {
    return (
        <button
            onClick={onClick}
            disabled={loading}
            className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-bottega-gold/[0.08] border border-bottega-gold/20 text-xs text-bottega-gold hover:bg-bottega-gold/15 hover:border-bottega-gold/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
        >
            {loading ? (
                <>
                    <div className="w-3 h-3 rounded-full border border-bottega-gold border-t-transparent animate-spin" />
                    {loadingLabel}
                </>
            ) : (
                label
            )}
        </button>
    );
}
