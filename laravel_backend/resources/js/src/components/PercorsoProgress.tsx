/**
 * @package La Bottega — PercorsoProgress
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Progresso visivo emozionale — l'opera che si compone (GAP 3b + FEAT 4)
 *
 * NON un numero "67%". Un'opera astratta che si costruisce:
 *   Fase 1 (Identita)          → contorno/sketch
 *   Fase 2 (Presenza Digitale) → colori base
 *   Fase 3 (Prima Vendita)     → dettagli e profondita
 *   Fase 4 (Ritmo)             → opera completa con cornice dorata
 */

import { type PercorsoStatus } from '@/api/bottegaApi';
import { useTranslation } from '@/hooks/useTranslation';

interface Props {
    percorso: PercorsoStatus;
}

const PHASES = [
    { key: 'percorso.fase_identita', num: 1 },
    { key: 'percorso.fase_presenza', num: 2 },
    { key: 'percorso.fase_vendita', num: 3 },
    { key: 'percorso.fase_ritmo', num: 4 },
];

export function PercorsoProgress({ percorso }: Props) {
    const { t } = useTranslation();
    const currentFase = percorso.fase;
    const completions = percorso.completions_by_fase;

    return (
        <div className="flex-none border-b border-white/5 px-4 py-3">
            <div className="max-w-2xl mx-auto">
                {/* Compact header with artwork visual + phase dots */}
                <div className="flex items-center gap-4">
                    {/* Mini artwork visual */}
                    <MiniArtwork fase={currentFase} />

                    {/* Phase indicators */}
                    <div className="flex-1 flex items-center gap-2">
                        {PHASES.map(({ key, num }) => {
                            const isDone = num < currentFase;
                            const isActive = num === currentFase;
                            const phaseData = completions[String(num)];
                            const progress = phaseData ? phaseData.completed / Math.max(phaseData.total, 1) : 0;

                            return (
                                <div key={num} className="flex-1" title={t(key)}>
                                    <div className="flex items-center gap-1.5 mb-1">
                                        <div className={`w-4 h-4 rounded-full flex items-center justify-center text-[8px] font-medium transition-all duration-500 ${
                                            isDone ? 'bg-bottega-gold/30 text-bottega-gold'
                                                : isActive ? 'bg-bottega-gold/10 text-bottega-gold border border-bottega-gold/30'
                                                : 'bg-white/5 text-gray-600'
                                        }`}>
                                            {isDone ? (
                                                <svg className="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={3}>
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                            ) : num}
                                        </div>
                                        <span className={`text-[9px] uppercase tracking-wider hidden sm:inline truncate ${
                                            isActive ? 'text-white/70' : isDone ? 'text-gray-500' : 'text-gray-700'
                                        }`}>
                                            {t(key)}
                                        </span>
                                    </div>
                                    {/* Progress bar per phase */}
                                    <div className="h-0.5 bg-white/5 rounded-full overflow-hidden">
                                        <div
                                            className={`h-full rounded-full transition-all duration-700 ease-out ${
                                                isDone ? 'bg-bottega-gold/40' : isActive ? 'bg-bottega-gold/30' : ''
                                            }`}
                                            style={{ width: `${isDone ? 100 : isActive ? Math.round(progress * 100) : 0}%` }}
                                        />
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </div>
        </div>
    );
}

/**
 * Mini artwork that evolves — shown inline in the progress bar.
 * Compact version of the full ArtworkVisual from CareerSummary.
 */
function MiniArtwork({ fase }: { fase: number }) {
    return (
        <div className="relative w-10 h-12 flex-shrink-0">
            {/* Frame — fase 4 */}
            <div className={`absolute inset-0 border rounded-sm transition-all duration-1000 ${
                fase >= 4 ? 'border-bottega-gold/60' : 'border-white/5'
            }`} />

            <svg viewBox="0 0 40 50" className="w-full h-full" aria-hidden="true">
                {/* Fase 1: sketch */}
                <g className={`transition-opacity duration-1000 ${fase >= 1 ? 'opacity-100' : 'opacity-0'}`}>
                    <path d="M10 42 C15 28, 18 20, 20 12 C22 20, 25 28, 30 42" fill="none"
                        stroke={fase >= 2 ? 'rgba(212,175,55,0.3)' : 'rgba(255,255,255,0.15)'}
                        strokeWidth="0.8" strokeLinecap="round"
                    />
                    <circle cx="20" cy="10" r="3" fill="none"
                        stroke={fase >= 2 ? 'rgba(212,175,55,0.3)' : 'rgba(255,255,255,0.12)'}
                        strokeWidth="0.5"
                    />
                </g>

                {/* Fase 2: color shapes */}
                <g className={`transition-opacity duration-1000 ${fase >= 2 ? 'opacity-100' : 'opacity-0'}`}>
                    <ellipse cx="17" cy="33" rx="7" ry="10" fill="rgba(212,175,55,0.08)" />
                    <ellipse cx="25" cy="31" rx="5" ry="8" fill="rgba(212,175,55,0.06)" />
                </g>

                {/* Fase 3: details */}
                <g className={`transition-opacity duration-1000 ${fase >= 3 ? 'opacity-100' : 'opacity-0'}`}>
                    <path d="M14 38 Q17 28 20 24 Q23 28 26 38" fill="rgba(212,175,55,0.12)"
                        stroke="rgba(212,175,55,0.2)" strokeWidth="0.3"
                    />
                    <circle cx="20" cy="10" r="2" fill="rgba(212,175,55,0.2)" />
                </g>

                {/* Fase 4: glow */}
                {fase >= 4 && (
                    <rect x="2" y="2" width="36" height="46" rx="1" fill="rgba(212,175,55,0.06)" />
                )}
            </svg>
        </div>
    );
}
