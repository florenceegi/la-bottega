/**
 * @package La Bottega — useAuth Hook
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Hook autenticazione Sanctum SSO — verifica utente loggato
 */

import { useEffect, useState } from 'react';
import { bottegaApi, type User, AuthError } from '@/api/bottegaApi';

interface AuthState {
    user: User | null;
    loading: boolean;
    authenticated: boolean;
}

export function useAuth(): AuthState {
    const [state, setState] = useState<AuthState>({
        user: null,
        loading: true,
        authenticated: false,
    });

    useEffect(() => {
        bottegaApi.getUser()
            .then(user => setState({ user, loading: false, authenticated: true }))
            .catch(err => {
                if (err instanceof AuthError) {
                    setState({ user: null, loading: false, authenticated: false });
                } else {
                    setState({ user: null, loading: false, authenticated: false });
                }
            });
    }, []);

    return state;
}
