"""
@package La Bottega — Python AI Service
@author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
@version 1.0.0 (FlorenceEGI — La Bottega)
@date 2026-04-15
@purpose FastAPI entry point — Maestro di Bottega AI orchestration layer.
         Receives prepared LLM messages from Laravel, calls Claude, returns response.
"""

import logging
import os

from dotenv import load_dotenv
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware

from app.routers import chat, health

load_dotenv(os.path.join(os.path.dirname(__file__), '..', '..', 'laravel_backend', '.env'))
load_dotenv(os.path.join(os.path.dirname(__file__), '..', '.env'), override=True)

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s [%(levelname)s] %(name)s: %(message)s',
    handlers=[logging.StreamHandler()],
)
logger = logging.getLogger('bottega-ai')

app = FastAPI(
    title='La Bottega AI Service',
    version='1.0.0',
    docs_url='/docs' if os.getenv('APP_ENV', 'production') != 'production' else None,
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=[
        'http://localhost:8005',
        'https://la-bottega.florenceegi.com',
    ],
    allow_methods=['POST', 'GET'],
    allow_headers=['*'],
)

app.include_router(health.router)
app.include_router(chat.router, prefix='/api/v1')
