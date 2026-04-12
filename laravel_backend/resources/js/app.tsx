/**
 * @package La Bottega — Frontend Entry Point
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Entry point React 19 per La Bottega
 */

import { createRoot } from 'react-dom/client';
import { App } from '@/App';

const container = document.getElementById('bottega-root');
if (container) {
    const root = createRoot(container);
    root.render(<App />);
}
