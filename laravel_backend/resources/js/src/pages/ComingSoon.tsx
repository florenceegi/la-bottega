/**
 * @package La Bottega — Coming Soon
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Landing Coming Soon — palette oro (#D4AF37) su navy (#1B2A4A)
 */

import { useTranslation } from '@/hooks/useTranslation';

export function ComingSoon() {
    const { t } = useTranslation();

    return (
        <div className="min-h-screen bg-[#1B2A4A] flex flex-col items-center justify-center px-6 text-center">
            <div className="max-w-lg">
                <div className="mb-8">
                    <div className="w-20 h-20 mx-auto rounded-full border-2 border-[#D4AF37] flex items-center justify-center mb-6">
                        <svg className="w-10 h-10 text-[#D4AF37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.5}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                        </svg>
                    </div>
                    <h1 className="text-4xl md:text-5xl font-light text-white tracking-wide mb-3">
                        {t('app.title')}
                    </h1>
                    <div className="w-16 h-px bg-[#D4AF37] mx-auto mb-6" />
                    <p className="text-[#D4AF37] text-lg font-light tracking-widest uppercase mb-8">
                        {t('coming_soon.title')}
                    </p>
                </div>

                <p className="text-gray-300 text-base leading-relaxed mb-8">
                    {t('coming_soon.description')}
                </p>

                <p className="text-gray-500 text-sm">
                    {t('coming_soon.ecosystem_label')}{' '}
                    <a
                        href="https://florenceegi.com"
                        className="text-[#D4AF37] hover:text-[#E5C76B] transition-colors"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        FlorenceEGI
                    </a>
                </p>
            </div>
        </div>
    );
}
