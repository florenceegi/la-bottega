/**
 * @package La Bottega — useMaestro Hook
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Hook chat Maestro — messaggi, invio, streaming, health check (GAP 5)
 */

import { useCallback, useEffect, useRef, useState } from 'react';
import { bottegaApi, type ChatMessage, type MaestroHealth } from '@/api/bottegaApi';
import { t } from '@/i18n/translations';

interface MaestroState {
    messages: ChatMessage[];
    sending: boolean;
    health: MaestroHealth | null;
    maestroAvailable: boolean;
    sendMessage: (text: string) => Promise<void>;
    clearMessages: () => void;
}

export function useMaestro(): MaestroState {
    const [messages, setMessages] = useState<ChatMessage[]>([]);
    const [sending, setSending] = useState(false);
    const [health, setHealth] = useState<MaestroHealth | null>(null);
    const healthInterval = useRef<ReturnType<typeof setInterval> | undefined>(undefined);

    const checkHealth = useCallback(async () => {
        try {
            const result = await bottegaApi.maestroHealth();
            setHealth(result);
        } catch {
            setHealth({ status: 'down' });
        }
    }, []);

    useEffect(() => {
        checkHealth();
        healthInterval.current = setInterval(checkHealth, 60_000);
        return () => clearInterval(healthInterval.current);
    }, [checkHealth]);

    const sendMessage = useCallback(async (text: string) => {
        const userMsg: ChatMessage = {
            role: 'user',
            content: text,
            timestamp: new Date().toISOString(),
        };
        setMessages(prev => [...prev, userMsg]);
        setSending(true);

        try {
            const { message } = await bottegaApi.maestroChat(text);
            setMessages(prev => [...prev, {
                ...message,
                timestamp: message.timestamp ?? new Date().toISOString(),
            }]);
        } catch {
            setMessages(prev => [...prev, {
                role: 'assistant',
                content: t('error.generic'),
                timestamp: new Date().toISOString(),
            }]);
        } finally {
            setSending(false);
        }
    }, []);

    const clearMessages = useCallback(() => setMessages([]), []);

    const maestroAvailable = health?.status !== 'down';

    return { messages, sending, health, maestroAvailable, sendMessage, clearMessages };
}
