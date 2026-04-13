/**
 * @package La Bottega — GoldButton
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Bottone primario oro — CTA principale della Bottega
 */

import type { ButtonHTMLAttributes } from 'react';

type Props = ButtonHTMLAttributes<HTMLButtonElement> & {
    variant?: 'primary' | 'ghost';
};

export function GoldButton({ variant = 'primary', className = '', children, ...props }: Props) {
    const base = 'inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-sm font-medium tracking-wide transition-all duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-bottega-gold/50 disabled:opacity-40 disabled:cursor-not-allowed';

    const variants = {
        primary: 'bg-bottega-gold text-bottega-navy hover:bg-bottega-gold-light hover:shadow-[0_0_20px_rgba(212,175,55,0.3)] active:scale-[0.97]',
        ghost: 'border border-bottega-gold/30 text-bottega-gold hover:border-bottega-gold hover:bg-bottega-gold/5 active:scale-[0.97]',
    };

    return (
        <button className={`${base} ${variants[variant]} ${className}`} {...props}>
            {children}
        </button>
    );
}
