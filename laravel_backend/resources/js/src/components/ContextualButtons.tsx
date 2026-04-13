/**
 * @package La Bottega — ContextualButtons
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Bottoni azione contestuali proposti dal Maestro inline nella chat (GAP 2 inline)
 */

import { type ContextualButton } from '@/api/bottegaApi';

interface Props {
    buttons: ContextualButton[];
}

const EGI_BASE = 'https://art.florenceegi.com';

const TOOL_ICONS: Record<string, string> = {
    microscopio: 'M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z',
    price_advisor: 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
    cantiere: 'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25',
    binocolo: 'M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z',
    coherence: 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
    navigate: 'M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25',
};

export function ContextualButtons({ buttons }: Props) {
    if (!buttons.length) return null;

    return (
        <div className="flex flex-wrap gap-2">
            {buttons.map((btn, i) => (
                <ContextButton key={i} button={btn} />
            ))}
        </div>
    );
}

function ContextButton({ button }: { button: ContextualButton }) {
    const iconPath = TOOL_ICONS[button.action] ?? TOOL_ICONS.navigate;

    const handleClick = () => {
        if (button.type === 'navigate' && button.target) {
            // GAP 2 — deep link to EGI with return param
            const url = button.target.startsWith('http')
                ? button.target
                : `${EGI_BASE}${button.target}`;
            const separator = url.includes('?') ? '&' : '?';
            window.open(`${url}${separator}from=bottega`, '_blank', 'noopener');
        }
        if (button.type === 'tool') {
            // TODO: open tool directly (Fase B+)
            console.info(`[Bottega] Tool action: ${button.action}`);
        }
        if (button.type === 'inline') {
            // TODO: inline action within Bottega (Fase A+)
            console.info(`[Bottega] Inline action: ${button.action}`);
        }
    };

    return (
        <button
            onClick={handleClick}
            className="inline-flex items-center gap-2 px-3.5 py-2 bg-bottega-gold/[0.06] border border-bottega-gold/20 rounded-lg text-bottega-gold text-xs tracking-wide hover:bg-bottega-gold/[0.12] hover:border-bottega-gold/40 transition-all duration-200"
        >
            <svg className="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.5}>
                <path strokeLinecap="round" strokeLinejoin="round" d={iconPath} />
            </svg>
            <span>{button.label}</span>
            {button.type === 'navigate' && (
                <svg className="w-3 h-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                </svg>
            )}
        </button>
    );
}
