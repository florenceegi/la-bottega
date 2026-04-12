@CLAUDE_ECOSYSTEM_CORE.md

# LA BOTTEGA — Contesto Specifico (Oracode OS3)

> Organo esterno: strumenti oggettivi per sviluppo artista come brand + valutazione informata per collezionisti.
> Stack: Laravel 12 + React 19 + Python FastAPI → PostgreSQL (schema bottega.*)
> URL: la-bottega.florenceegi.com | EC2: i-0940cdb7b955d1632 | Path: /home/forge/la-bottega.florenceegi.com
> Branch attivo: main | Deploy: Nginx → PHP 8.3-FPM (socket)

---

## Ruolo nell'Organismo

```
La Bottega e un organo ESTERNO — non un modulo di EGI.
Comunica con l'ecosistema esclusivamente via API interne.

Consuma da EGI:
  Opere, collezioni, traits, prezzi, COA, mint, bind (FlorenceEGI Core)
  NPE Council, Pricing Advisor, Social (campagne, publishing)
  EgiliService (economia cross-organ)
  Biography + BiographyChapter (bio a capitoli)
  CollectionSplitterService (coerenza)
  RAG Creator/Collector (schema bottega_rag_* su EGI)

Consuma da EGI-Credential:
  Wallet credenziali, verifica, EgizzazioneService

Consuma da NATAN_LOC:
  Skill extraction, web verification (badge Oro/Grigio)

Espone verso EGI:
  Credibility Score (Lente), Creator Profile, Microscopio Score, eventi analytics
```

---

## Stack

```
Frontend   → React 19 + TypeScript + Tailwind + Vite
Backend    → Laravel 12 (PHP 8.3) + Sanctum SSO + Spatie RBAC
Python     → FastAPI (Maestro AI, NextStep Engine, Valuator)
Database   → PostgreSQL RDS eu-north-1 (florenceegi)
             DB_SEARCH_PATH: bottega,core,public
             Schema bottega: creator_profiles, collector_profiles, step_completions,
                             maestro_conversations, opportunities, profile_events,
                             microscopio_reports, prospect_lists
RAG        → Su EGI (bottega_rag_creator.*, bottega_rag_collector.*) — consumati via API
LLM        → Claude (Maestro AI)
Cache      → Redis :6379 prefix bottega:
Auth       → Sanctum cookie cross-subdomain (.florenceegi.com)
```

---

## Componenti Principali

```
# Il Maestro di Bottega — cuore dell'organo
Maestro Creator (artist)   → guida, diagnostica, next step, apre strumenti
Maestro Collector (collector) → interpreta, confronta, valuta

# Strumenti Creator (8)
Microscopio      → diagnosi profilo (wrappa Egi model)
Sestante         → posizionamento comparato (wrappa EgiService)
Price Advisor    → prezzi coerenti (wrappa PricingAdvisorService, 120 Egili)
Cantiere         → bio + statement (wrappa Biography + BiographyChapter)
Coherence Check  → coerenza collezione (wrappa CollectionSplitterService)
Binocolo         → opportunita esterne (NUOVO — RAG Creator)
Market Pulse     → trend mercato (parzialmente nuovo)
Visibility Tracker → analytics profilo (NUOVO — richiede eventi tracking)

# Strumenti Collector (4)
Lente       → Credibility Score (NUOVO — composito da dati EGI + Credential)
Registro    → storico trasparente (wrappa EgiBlockchain + CoaSnapshot)
Bilanciere  → comparazione prezzi (wrappa dati vendite + PricingAdvisorService)
Portafoglio → collection value tracker (NUOVO)

# Percorsi Creator (3 x 16 step = 48 step totali)
ZERO      → dalle fondamenta alla prima vendita (4 fasi, 16 step)
CRESCITA  → da prima vendita a sistema (4 fasi, 16 step)
MERCATO   → verso il professionismo (4 fasi, 16 step)
```

---

## File Critici (da aggiornare man mano che si costruisce)

```
# Laravel
app/Http/Controllers/MaestroController.php
app/Http/Controllers/MicroscopioController.php
app/Http/Controllers/PriceAdvisorController.php
app/Http/Controllers/PercorsoController.php
app/Services/MaestroProxyService.php
app/Services/EgiApiClient.php
app/Services/BottegaPriceAdvisorService.php
app/Services/MicroscopioService.php
routes/api.php

# Python
python_ai_service/app/services/maestro_service.py
python_ai_service/app/services/next_step_engine.py
python_ai_service/app/routers/maestro.py

# Frontend
src/components/MaestroChat.tsx
src/components/ContextualButtons.tsx
src/components/CareerSummary.tsx
src/components/PercorsoProgress.tsx
```

---

## P0 Specifici LA BOTTEGA

| # | Regola | Enforcement |
|---|--------|-------------|
| P0-BOT-1 | **FlorenceEGI unico marketplace** | MAI suggerire Artsy/Etsy/OpenSea/piattaforme concorrenti |
| P0-BOT-2 | **Gallerie MAI come canale vendita** | Sono la concorrenza diretta (40-50% commissione) |
| P0-BOT-3 | **I prezzi non si abbassano MAI** | Edizioni limitate si, sconti no |
| P0-BOT-4 | **Un solo next step alla volta** | Il Maestro non anticipa mai il percorso completo |
| P0-BOT-5 | **Nessuna credenziale senza evidenza** | NATAN verifica. Zero eccezioni |
| P0-BOT-6 | **COA = argomento di vendita** | Non un dettaglio tecnico — il differenziale competitivo |
| P0-BOT-7 | **Ogni transazione su FlorenceEGI** | Anche contatti esterni devono concludere su piattaforma |

---

## User Types

| user_type | Ruolo Spatie | Maestro | Strumenti |
|-----------|-------------|---------|-----------|
| `artist` | `credential_user` | Creator | Microscopio, Sestante, Price Advisor, Cantiere, Coherence Check, Binocolo, Market Pulse, Visibility Tracker |
| `collector` | `credential_collector` | Collector | Lente, Registro, Bilanciere, Portafoglio |
| `event_organizer` | `credential_issuer` | — | Issuer EventCredential |
| `gallery` | `credential_issuer` | — | Issuer InstitutionCredential |

---

## Valori Immutabili — MAI toccare senza approvazione Fabio

```
tokens_per_egili           = 80     # economia Egili v3.0.0
egili_per_query            = 296    # media 23.609 token x 0,0125
Price Advisor cost         = 120 Egili  # wrappa NPE, stesso costo
Coherence Score soglia     = 70%    # sotto = outlier segnalati
Profile Completeness soglia = 80%   # per Step 6 Percorso ZERO
Bio Origine min            = 200 parole
Artist statement max       = 20 parole
Ed.10 pricing              = 30-40% originale
Ed.25 pricing              = 20-30% originale
Ed.50 pricing              = 15-20% originale
```

---

## Costi Egili — SEMPRE da DB

```
MAI hardcoded. SEMPRE da core.ai_feature_pricing.
Fabio deve poter cambiare senza deploy.

Pattern:
  $cost = $this->egiApiClient->getFeaturePricing('bottega.microscopio');
```

---

## Pipeline Post-Commit

```
1. git push origin main
2. GitHub Actions → SSM EC2 (i-0940cdb7b955d1632):
   cd /home/forge/la-bottega.florenceegi.com
   && git pull origin main
   && cd laravel_backend && composer install --no-dev
   && php artisan migrate --force
   && php artisan config:cache && php artisan view:clear
3. Se modificati file TS/TSX/CSS: npm run build
4. Se modificati file Python: sudo supervisorctl restart bottega-fastapi
```

---

## Checklist Pre-Risposta

```
1. Ho TUTTE le info necessarie?           NO  → CHIEDI (P0-1)
2. Sto wrappando un servizio EGI?         SI  → verifica che esista con grep
3. Sto creando qualcosa di nuovo?         SI  → organ_index --search prima
4. Costi Egili hardcoded?                 SI  → MAI — usa ai_feature_pricing
5. Suggerisco piattaforme concorrenti?    SI  → STOP (P0-BOT-1)
6. Suggerisco gallerie come vendita?      SI  → STOP (P0-BOT-2)
7. i18n in tutte 6 lingue?               NO  → NON PROCEDERE (P0-9)
8. DOC-SYNC eseguito?                     NO  → NON CHIUDERE (P0-11)
```

---

## Audit Oracode

Target ID: **T-008** | SSOT docs: `EGI-DOC/docs/la-bottega/`
Missione: M-050

---

*Oracode OS3.0 — FlorenceEGI — La Bottega CLAUDE.md v1.0.0 — 2026-04-12*
