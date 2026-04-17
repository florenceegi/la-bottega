/**
 * @package La Bottega — BottegaLayout
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.1.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Layout 3 colonne: ToolSidebar | MaestroChat | CareerSummary (GAP 1 + GAP 5)
 */

import { useState, useEffect, useCallback } from 'react';
import { MaestroChat } from '@/components/MaestroChat';
import { CareerSummary } from '@/components/CareerSummary';
import { ToolSidebar } from '@/components/ToolSidebar';
import { PercorsoProgress } from '@/components/PercorsoProgress';
import { OnboardingOverlay } from '@/components/OnboardingOverlay';
import { MicroscopioReport } from '@/components/MicroscopioReport';
import { BinocoloReport } from '@/components/BinocoloReport';
import { MarketPulseReport } from '@/components/MarketPulseReport';
import { VisibilityTrackerReport } from '@/components/VisibilityTrackerReport';
import { PriceAdvisorPanel } from '@/components/PriceAdvisorPanel';
import { usePercorso } from '@/hooks/usePercorso';
import { useAuth } from '@/hooks/useAuth';
import { useMaestroHealth } from '@/hooks/useMaestroHealth';
import { bottegaApi, type OnboardingResult } from '@/api/bottegaApi';
import { useTranslation } from '@/hooks/useTranslation';
import { type Locale } from '@/i18n/translations';

export function BottegaLayout() {
    const { t, locale, changeLocale } = useTranslation();
    const auth = useAuth();
    const percorso = usePercorso();
    const health = useMaestroHealth();
    const maestroDown = health.status === 'down';

    const [showOnboarding, setShowOnboarding] = useState(false);
    const [onboardingResult, setOnboardingResult] = useState<OnboardingResult | null>(null);
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [microscopioOpen, setMicroscopioOpen] = useState(false);
    const [binocoloOpen, setBinocoloOpen] = useState(false);
    const [marketPulseOpen, setMarketPulseOpen] = useState(false);
    const [visibilityOpen, setVisibilityOpen] = useState(false);
    const [priceAdvisorOpen, setPriceAdvisorOpen] = useState(false);

    // Check if first visit (no percorso assigned)
    useEffect(() => {
        if (!percorso.loading && !percorso.status?.percorso) {
            setShowOnboarding(true);
        }
    }, [percorso.loading, percorso.status]);

    const handleOnboardingComplete = useCallback(() => {
        setShowOnboarding(false);
        percorso.refresh();
    }, [percorso]);

    const user = auth.authenticated && auth.user ? auth.user : null;

    return (
        <div className="h-screen bg-bottega-navy flex overflow-hidden">
            {/* Onboarding overlay — primo accesso (GAP 3a) */}
            {showOnboarding && user && (
                <OnboardingOverlay
                    user={user}
                    onComplete={(result) => {
                        setOnboardingResult(result);
                        handleOnboardingComplete();
                    }}
                />
            )}

            {/* Left: Tool Sidebar (GAP 1 + GAP 5 fallback) */}
            <ToolSidebar
                expanded={sidebarOpen || maestroDown}
                onToggle={() => setSidebarOpen(prev => !prev)}
                maestroDown={maestroDown}
                onToolOpen={(name) => {
                    if (name === 'microscopio') setMicroscopioOpen(true);
                    if (name === 'binocolo') setBinocoloOpen(true);
                    if (name === 'market_pulse') setMarketPulseOpen(true);
                    if (name === 'visibility_tracker') setVisibilityOpen(true);
                    if (name === 'price_advisor') setPriceAdvisorOpen(true);
                }}
            />

            {/* Center: Header + Chat */}
            <main className="flex-1 flex flex-col min-w-0">
                {/* Header bar */}
                <header className="flex items-center justify-between px-4 py-3 border-b border-white/5">
                    <button
                        onClick={() => setSidebarOpen(prev => !prev)}
                        className="lg:hidden p-2 text-gray-400 hover:text-bottega-gold transition-colors"
                        aria-label={t('tools.title')}
                    >
                        <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.5}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <div className="flex items-center gap-3">
                        <div className="w-8 h-8 rounded-full border border-bottega-gold/30 flex items-center justify-center">
                            <svg className="w-4 h-4 text-bottega-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.5}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                            </svg>
                        </div>
                        <div>
                            <h1 className="text-sm font-medium text-white">{t('app.title')}</h1>
                            <p className="text-xs text-gray-500">{t('app.tagline')}</p>
                        </div>
                    </div>
                    <div className="flex items-center gap-3">
                        <select
                            value={locale}
                            onChange={e => changeLocale(e.target.value as Locale)}
                            className="bg-transparent text-bottega-text-muted text-xs border border-white/10 rounded px-2 py-1 focus:outline-none focus:border-bottega-gold/30"
                            aria-label={t('a11y.language_select')}
                        >
                            <option value="it">IT</option>
                            <option value="en">EN</option>
                            <option value="de">DE</option>
                            <option value="es">ES</option>
                            <option value="fr">FR</option>
                            <option value="pt">PT</option>
                        </select>
                        {user && <span className="text-xs text-gray-500 hidden sm:inline">{user.name}</span>}
                    </div>
                </header>

                {/* Chat area */}
                {user && (
                    <MaestroChat
                        user={user}
                        maestroDown={maestroDown}
                        onboardingResult={onboardingResult}
                    />
                )}
            </main>

            {/* Right: Career Summary + Percorso (desktop only) */}
            <aside className="hidden xl:flex flex-col w-80 border-l border-white/5 overflow-y-auto">
                <CareerSummary percorso={percorso.status} />
                {percorso.status?.percorso && (
                    <PercorsoProgress percorso={percorso.status} />
                )}
            </aside>

            {/* Microscopio Report overlay */}
            <MicroscopioReport open={microscopioOpen} onClose={() => setMicroscopioOpen(false)} />

            {/* Binocolo Report overlay */}
            <BinocoloReport open={binocoloOpen} onClose={() => setBinocoloOpen(false)} />

            {/* Market Pulse Report overlay */}
            <MarketPulseReport open={marketPulseOpen} onClose={() => setMarketPulseOpen(false)} />

            {/* Visibility Tracker Report overlay */}
            <VisibilityTrackerReport open={visibilityOpen} onClose={() => setVisibilityOpen(false)} />

            {/* Price Advisor Panel overlay */}
            <PriceAdvisorPanel open={priceAdvisorOpen} onClose={() => setPriceAdvisorOpen(false)} />
        </div>
    );
}
