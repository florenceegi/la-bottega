/**
 * @package La Bottega — MaestroChat
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Chat centrale col Maestro — messaggi + input + bottoni contestuali (GAP 5 fallback)
 */

import { useCallback, useEffect, useRef, useState } from 'react';
import { bottegaApi, type User, type ChatMessage, type OnboardingResult } from '@/api/bottegaApi';
import { ContextualButtons } from '@/components/ContextualButtons';
import { useTranslation } from '@/hooks/useTranslation';
import DOMPurify from 'dompurify';

interface Props {
    user: User;
    maestroDown: boolean;
    onboardingResult: OnboardingResult | null;
}

export function MaestroChat({ user, maestroDown, onboardingResult }: Props) {
    const { t } = useTranslation();
    const [messages, setMessages] = useState<ChatMessage[]>([]);
    const [input, setInput] = useState('');
    const [sending, setSending] = useState(false);
    const scrollRef = useRef<HTMLDivElement>(null);
    const inputRef = useRef<HTMLTextAreaElement>(null);

    // Inject onboarding welcome as first assistant message
    useEffect(() => {
        if (onboardingResult && messages.length === 0) {
            const ctx = onboardingResult.welcome_context;
            const welcomeContent = [
                `**${t('maestro.welcome')}, ${user.name}!**`,
                '',
                ...ctx.strengths.map((s: string) => `✦ ${s}`),
                '',
                ...ctx.gaps.map((g: string) => `○ ${g}`),
                '',
                `*${ctx.first_step_description}*`,
            ].join('\n');

            setMessages([{
                role: 'assistant',
                content: welcomeContent,
                timestamp: new Date().toISOString(),
            }]);
        }
    }, [onboardingResult, messages.length, user.name, t]);

    // Auto-scroll on new messages
    useEffect(() => {
        if (scrollRef.current) {
            scrollRef.current.scrollTop = scrollRef.current.scrollHeight;
        }
    }, [messages, sending]);

    useEffect(() => { inputRef.current?.focus(); }, []);

    const handleSend = useCallback(async () => {
        const text = input.trim();
        if (!text || sending || maestroDown) return;

        const userMsg: ChatMessage = { role: 'user', content: text, timestamp: new Date().toISOString() };
        setMessages(prev => [...prev, userMsg]);
        setInput('');
        setSending(true);

        try {
            const { message } = await bottegaApi.maestroChat(text);
            setMessages(prev => [...prev, { ...message, timestamp: message.timestamp ?? new Date().toISOString() }]);
        } catch {
            setMessages(prev => [...prev, {
                role: 'assistant',
                content: t('error.generic'),
                timestamp: new Date().toISOString(),
            }]);
        } finally {
            setSending(false);
            inputRef.current?.focus();
        }
    }, [input, sending, maestroDown, t]);

    const handleKeyDown = useCallback((e: React.KeyboardEvent<HTMLTextAreaElement>) => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); handleSend(); }
    }, [handleSend]);

    const handleTextareaChange = useCallback((e: React.ChangeEvent<HTMLTextAreaElement>) => {
        setInput(e.target.value);
        const el = e.target;
        el.style.height = 'auto';
        el.style.height = `${Math.min(el.scrollHeight, 120)}px`;
    }, []);

    return (
        <div className="flex-1 flex flex-col min-h-0">
            {/* Messages */}
            <div ref={scrollRef} className="flex-1 overflow-y-auto px-4 py-6 scroll-smooth" style={{ scrollbarWidth: 'thin', scrollbarColor: 'rgba(212,175,55,0.15) transparent' }}>
                {messages.length === 0 && !sending && (
                    <EmptyState maestroDown={maestroDown} />
                )}

                <div className="max-w-2xl mx-auto space-y-5">
                    {messages.map((msg, i) => (
                        <MessageBubble key={i} message={msg} />
                    ))}
                    {sending && <TypingIndicator />}
                </div>
            </div>

            {/* Input */}
            <div className="flex-none border-t border-white/5 px-4 py-3">
                {maestroDown ? (
                    <div className="max-w-2xl mx-auto bg-amber-900/10 border border-amber-500/20 rounded-xl px-4 py-3 text-center">
                        <p className="text-amber-400/80 text-sm">{t('maestro.offline')}</p>
                        <p className="text-amber-400/50 text-xs mt-1">{t('maestro.offline_hint')}</p>
                    </div>
                ) : (
                    <form onSubmit={e => { e.preventDefault(); handleSend(); }} className="max-w-2xl mx-auto flex items-end gap-3">
                        <textarea
                            ref={inputRef}
                            value={input}
                            onChange={handleTextareaChange}
                            onKeyDown={handleKeyDown}
                            placeholder={t('maestro.placeholder')}
                            rows={1}
                            disabled={sending}
                            className="flex-1 bg-white/[0.03] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 resize-none focus:outline-none focus:border-bottega-gold/30 focus:ring-1 focus:ring-bottega-gold/10 transition-all duration-200 disabled:opacity-50"
                            aria-label={t('maestro.placeholder')}
                        />
                        <button
                            type="submit"
                            disabled={!input.trim() || sending}
                            className="flex-shrink-0 w-10 h-10 rounded-xl bg-bottega-gold/10 border border-bottega-gold/30 text-bottega-gold flex items-center justify-center hover:bg-bottega-gold/20 hover:border-bottega-gold/50 transition-all duration-200 disabled:opacity-30 disabled:cursor-not-allowed"
                            aria-label={t('maestro.send')}
                        >
                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                                <path strokeLinecap="round" strokeLinejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                            </svg>
                        </button>
                    </form>
                )}
            </div>
        </div>
    );
}

function MessageBubble({ message }: { message: ChatMessage }) {
    const { t } = useTranslation();
    const isUser = message.role === 'user';

    return (
        <div className={`flex ${isUser ? 'justify-end' : 'justify-start'} animate-fade-up`}>
            <div className="max-w-[85%]">
                {!isUser && (
                    <div className="flex items-center gap-2 mb-1.5">
                        <div className="w-5 h-5 rounded-full border border-bottega-gold/30 flex items-center justify-center">
                            <div className="w-2 h-2 rounded-full bg-bottega-gold/60" />
                        </div>
                        <span className="text-[10px] text-bottega-gold/40 uppercase tracking-widest">{t('maestro.label')}</span>
                    </div>
                )}
                <div className={`rounded-2xl px-4 py-3 text-sm leading-relaxed ${
                    isUser
                        ? 'bg-bottega-gold/10 border border-bottega-gold/20 text-white'
                        : 'bg-white/[0.03] border border-white/5 text-gray-300'
                }`}>
                    <div dangerouslySetInnerHTML={{ __html: DOMPurify.sanitize(formatMessage(message.content)) }} />
                </div>
                {message.buttons && message.buttons.length > 0 && (
                    <div className="mt-2">
                        <ContextualButtons buttons={message.buttons} />
                    </div>
                )}
            </div>
        </div>
    );
}

function TypingIndicator() {
    return (
        <div className="flex items-start gap-2 animate-fade-up">
            <div className="w-5 h-5 rounded-full border border-bottega-gold/30 flex items-center justify-center">
                <div className="w-2 h-2 rounded-full bg-bottega-gold/60" />
            </div>
            <div className="bg-white/[0.03] border border-white/5 rounded-2xl px-4 py-3">
                <div className="flex items-center gap-1.5">
                    <div className="w-1.5 h-1.5 rounded-full bg-bottega-gold/40 maestro-typing-dot" />
                    <div className="w-1.5 h-1.5 rounded-full bg-bottega-gold/40 maestro-typing-dot" />
                    <div className="w-1.5 h-1.5 rounded-full bg-bottega-gold/40 maestro-typing-dot" />
                </div>
            </div>
        </div>
    );
}

function EmptyState({ maestroDown }: { maestroDown: boolean }) {
    const { t } = useTranslation();
    return (
        <div className="h-full flex flex-col items-center justify-center text-center px-6">
            <div className="w-20 h-20 mb-6 rounded-full border border-bottega-gold/20 flex items-center justify-center glow-gold-subtle">
                <svg className="w-10 h-10 text-bottega-gold/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={0.8}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                </svg>
            </div>
            <h2 className="text-lg font-light text-white/80 mb-2">
                {maestroDown ? t('maestro.offline') : t('maestro.welcome')}
            </h2>
            <p className="text-sm text-gray-500 max-w-xs">
                {maestroDown ? t('maestro.offline_hint') : t('maestro.welcome_subtitle')}
            </p>
        </div>
    );
}

function formatMessage(content: string): string {
    return content
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/\n/g, '<br />');
}
