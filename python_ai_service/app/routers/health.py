"""
@package La Bottega — Health Router
@author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
@version 1.0.0 (FlorenceEGI — La Bottega)
@date 2026-04-15
@purpose Health check endpoint per monitoring e deploy verification.
"""

import os

from fastapi import APIRouter

router = APIRouter(tags=['health'])


@router.get('/health')
async def health_check():
    return {
        'status': 'ok',
        'service': 'bottega-ai',
        'version': '1.0.0',
        'anthropic_configured': bool(os.getenv('ANTHROPIC_API_KEY')),
    }
