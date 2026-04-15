"""
@package La Bottega — Claude Service
@author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
@version 1.0.0 (FlorenceEGI — La Bottega)
@date 2026-04-15
@purpose Async Claude API client with timeout, token tracking, error handling.
"""

import asyncio
import logging
import os

import anthropic

logger = logging.getLogger('bottega-ai.claude')

DEFAULT_MODEL = 'claude-sonnet-4-20250514'
DEFAULT_MAX_TOKENS = 2048
DEFAULT_TEMPERATURE = 0.7
TIMEOUT_SECONDS = 30.0


async def chat_completion(messages: list[dict], model: str | None = None) -> dict:
    """
    Send messages to Claude Messages API and return the response.

    Expects messages in OpenAI-style format:
      [{"role": "system", "content": "..."}, {"role": "user", "content": "..."}, ...]

    The system message is extracted and passed as the `system` parameter.
    Returns dict with: message, tokens_used, model_used.
    """
    api_key = os.getenv('ANTHROPIC_API_KEY')
    if not api_key:
        logger.error('ANTHROPIC_API_KEY not configured')
        return {
            'message': None,
            'tokens_used': 0,
            'model_used': 'none',
            'error': 'API key not configured',
        }

    model = model or os.getenv('BOTTEGA_AI_MODEL', DEFAULT_MODEL)
    max_tokens = int(os.getenv('BOTTEGA_AI_MAX_TOKENS', DEFAULT_MAX_TOKENS))
    temperature = float(os.getenv('BOTTEGA_AI_TEMPERATURE', DEFAULT_TEMPERATURE))

    system_prompt = None
    chat_messages = []
    for msg in messages:
        if msg['role'] == 'system':
            system_prompt = msg['content']
        else:
            chat_messages.append({'role': msg['role'], 'content': msg['content']})

    client = anthropic.AsyncAnthropic(api_key=api_key)

    try:
        kwargs = {
            'model': model,
            'max_tokens': max_tokens,
            'temperature': temperature,
            'messages': chat_messages,
        }
        if system_prompt:
            kwargs['system'] = system_prompt

        response = await asyncio.wait_for(
            client.messages.create(**kwargs),
            timeout=TIMEOUT_SECONDS,
        )

        content = response.content[0].text if response.content else ''
        tokens_in = response.usage.input_tokens
        tokens_out = response.usage.output_tokens

        logger.info(
            'Claude response: model=%s tokens_in=%d tokens_out=%d',
            response.model, tokens_in, tokens_out,
        )

        return {
            'message': content,
            'tokens_used': tokens_in + tokens_out,
            'model_used': response.model,
            'error': None,
        }

    except asyncio.TimeoutError:
        logger.warning('Claude API timeout after %.0fs', TIMEOUT_SECONDS)
        return {
            'message': None,
            'tokens_used': 0,
            'model_used': model,
            'error': 'timeout',
        }
    except anthropic.AuthenticationError:
        logger.error('Claude API authentication failed')
        return {
            'message': None,
            'tokens_used': 0,
            'model_used': model,
            'error': 'authentication_failed',
        }
    except anthropic.RateLimitError:
        logger.warning('Claude API rate limited')
        return {
            'message': None,
            'tokens_used': 0,
            'model_used': model,
            'error': 'rate_limited',
        }
    except Exception as e:
        logger.error('Claude API error: %s', str(e))
        return {
            'message': None,
            'tokens_used': 0,
            'model_used': model,
            'error': str(e),
        }
