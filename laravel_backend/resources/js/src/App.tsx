/**
 * @package La Bottega — App Component
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.1.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Root component — AuthGuard + BottegaLayout
 */

import { AuthGuard } from '@/components/AuthGuard';
import { BottegaLayout } from '@/components/BottegaLayout';

export function App() {
    return (
        <AuthGuard>
            <BottegaLayout />
        </AuthGuard>
    );
}
