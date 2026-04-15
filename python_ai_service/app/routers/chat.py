"""
@package La Bottega — Chat Router
@author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
@version 1.0.0 (FlorenceEGI — La Bottega)
@date 2026-04-15
@purpose Endpoint chat Maestro di Bottega — riceve messaggi preparati da Laravel,
         chiama Claude via claude_service, ritorna risposta strutturata.
"""

import logging

from fastapi import APIRouter, HTTPException
from pydantic import BaseModel

from app.services.claude_service import chat_completion

logger = logging.getLogger('bottega-ai.chat')

router = APIRouter(tags=['chat'])


class ChatRequest(BaseModel):
    messages: list[dict]
    model: str | None = None


class ChatResponse(BaseModel):
    message: str | None
    tokens_used: int
    model_used: str
    error: str | None = None


@router.post('/chat', response_model=ChatResponse)
async def maestro_chat(request: ChatRequest):
    """
    Riceve i messaggi LLM pre-costruiti da Laravel (system + history + user)
    e li inoltra a Claude. Laravel gestisce contesto, memoria e prompt.
    Python gestisce solo la chiamata LLM async con timeout.
    """
    if not request.messages:
        raise HTTPException(status_code=422, detail='messages array is empty')

    result = await chat_completion(request.messages, model=request.model)

    if result['error'] and result['message'] is None:
        logger.warning('Chat failed: %s', result['error'])

    return ChatResponse(**result)
