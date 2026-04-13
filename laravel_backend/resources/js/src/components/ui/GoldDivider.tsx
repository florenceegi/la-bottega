/**
 * @package La Bottega — GoldDivider
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Linea divisoria oro — design element ricorrente
 */

export function GoldDivider({ className = '' }: { className?: string }) {
    return (
        <div
            className={`h-px bg-gradient-to-r from-transparent via-bottega-gold to-transparent ${className}`}
            role="separator"
        />
    );
}
