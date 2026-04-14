/**
 * @package La Bottega — useAuth Hook
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-14
 * @purpose Hook autenticazione Bearer token — login/logout/register in-app
 */

import { useEffect, useState, useCallback } from 'react';
import { bottegaApi, type User, AuthError } from '@/api/bottegaApi';

interface AuthState {
    user: User | null;
    loading: boolean;
    authenticated: boolean;
}

interface AuthActions {
    user: User | null;
    loading: boolean;
    authenticated: boolean;
    login: (email: string, password: string) => Promise<void>;
    logout: () => void;
    register: (data: {
        name: string;
        email: string;
        password: string;
        password_confirmation: string;
    }) => Promise<void>;
}

export function useAuth(): AuthActions {
    const [state, setState] = useState<AuthState>({
        user: null,
        loading: true,
        authenticated: false,
    });

    const validateToken = useCallback(() => {
        if (!bottegaApi.hasToken()) {
            setState({ user: null, loading: false, authenticated: false });
            return;
        }

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

    useEffect(() => {
        validateToken();

        const handleAuthChange = () => validateToken();
        window.addEventListener('bottega:auth-change', handleAuthChange);
        return () => window.removeEventListener('bottega:auth-change', handleAuthChange);
    }, [validateToken]);

    const login = useCallback(async (email: string, password: string) => {
        const { user } = await bottegaApi.login(email, password);
        setState({ user, loading: false, authenticated: true });
        window.dispatchEvent(new CustomEvent('bottega:auth-change'));
    }, []);

    const logout = useCallback(() => {
        bottegaApi.logout();
        setState({ user: null, loading: false, authenticated: false });
    }, []);

    const register = useCallback(async (data: {
        name: string;
        email: string;
        password: string;
        password_confirmation: string;
    }) => {
        const { user, token } = await bottegaApi.register(data);
        localStorage.setItem('bottega_token', token);
        localStorage.setItem('bottega_user', JSON.stringify(user));
        setState({ user, loading: false, authenticated: true });
        window.dispatchEvent(new CustomEvent('bottega:auth-change'));
    }, []);

    return {
        ...state,
        login,
        logout,
        register,
    };
}
