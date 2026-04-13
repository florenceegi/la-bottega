/**
 * @package La Bottega — ToolSidebar
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Sidebar strumenti gia usati (GAP 1) + fallback senza Maestro (GAP 5)
 */

import { useEffect, useState } from 'react';
import { bottegaApi, type ToolExecution } from '@/api/bottegaApi';
import { useTranslation } from '@/hooks/useTranslation';

interface Props {
    expanded: boolean;
    onToggle: () => void;
    maestroDown: boolean;
}

const TOOL_META: Record<string, { icon: string; key: string }> = {
    microscopio: {
        icon: 'M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z',
        key: 'tools.microscopio',
    },
    sestante: {
        icon: 'M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z',
        key: 'tools.sestante',
    },
    price_advisor: {
        icon: 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        key: 'tools.price_advisor',
    },
    cantiere: {
        icon: 'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25',
        key: 'tools.cantiere',
    },
    binocolo: {
        icon: 'M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z',
        key: 'tools.binocolo',
    },
    coherence_check: {
        icon: 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        key: 'tools.coherence',
    },
    market_pulse: {
        icon: 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z',
        key: 'tools.market_pulse',
    },
    visibility_tracker: {
        icon: 'M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6',
        key: 'tools.visibility',
    },
};

export function ToolSidebar({ expanded, onToggle, maestroDown }: Props) {
    const { t } = useTranslation();
    const [tools, setTools] = useState<ToolExecution[]>([]);

    useEffect(() => {
        bottegaApi.toolsUnlocked().then(setTools);
    }, []);

    const hasTools = tools.length > 0;

    return (
        <nav
            className={`flex-none flex flex-col items-center py-4 gap-1 border-r border-white/5 bg-bottega-navy-dark transition-all duration-300 ${
                expanded ? 'w-48' : 'w-14'
            }`}
            aria-label={t('tools.title')}
        >
            {/* Toggle */}
            <button
                onClick={onToggle}
                className="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 hover:text-bottega-gold hover:bg-white/5 transition-colors mb-2"
                aria-label={t('tools.title')}
            >
                <svg className={`w-4 h-4 transition-transform duration-300 ${expanded ? 'rotate-180' : ''}`} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.5}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            {/* Maestro status */}
            <div className="mb-3 flex items-center gap-2">
                <div className={`w-2 h-2 rounded-full ${maestroDown ? 'bg-amber-400' : 'bg-emerald-400'}`} />
                {expanded && (
                    <span className={`text-[10px] uppercase tracking-wider ${maestroDown ? 'text-amber-400' : 'text-emerald-400/60'}`}>
                        {maestroDown ? t('maestro.status_offline') : t('maestro.status_online')}
                    </span>
                )}
            </div>

            {/* Separator */}
            <div className="w-6 h-px bg-white/5 mb-2" />

            {/* Tools list */}
            {hasTools ? (
                <div className="flex flex-col items-center gap-1 animate-stagger">
                    {tools.map(tool => {
                        const meta = TOOL_META[tool.tool_name];
                        if (!meta) return null;
                        return (
                            <ToolButton
                                key={tool.tool_name}
                                name={tool.tool_name}
                                icon={meta.icon}
                                label={t(meta.key)}
                                expanded={expanded}
                            />
                        );
                    })}
                </div>
            ) : (
                <div className="flex-1 flex items-center justify-center px-2">
                    {expanded ? (
                        <p className="text-[10px] text-gray-600 text-center leading-relaxed">
                            {t('tools.locked_hint')}
                        </p>
                    ) : (
                        <div className="w-8 h-8 rounded-full border border-dashed border-white/10 flex items-center justify-center">
                            <svg className="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.5}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085" />
                            </svg>
                        </div>
                    )}
                </div>
            )}
        </nav>
    );
}

function ToolButton({ name, icon, label, expanded }: {
    name: string; icon: string; label: string; expanded: boolean;
}) {
    return (
        <button
            onClick={() => console.info(`[Bottega] Open tool: ${name}`)}
            title={expanded ? undefined : label}
            className={`group flex items-center gap-2.5 rounded-lg text-gray-500 hover:text-bottega-gold hover:bg-bottega-gold/[0.06] transition-all duration-200 ${
                expanded ? 'w-full px-3 py-2' : 'w-10 h-10 justify-center'
            }`}
            aria-label={label}
        >
            <svg className="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.5}>
                <path strokeLinecap="round" strokeLinejoin="round" d={icon} />
            </svg>
            {expanded && (
                <span className="text-xs truncate">{label}</span>
            )}
            {/* Tooltip when collapsed */}
            {!expanded && (
                <span className="absolute left-full ml-2 px-2 py-1 bg-bottega-navy-dark border border-white/10 rounded text-[10px] text-gray-300 whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50">
                    {label}
                </span>
            )}
        </button>
    );
}
