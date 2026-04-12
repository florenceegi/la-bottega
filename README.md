# La Bottega — FlorenceEGI

> Nella nostra Bottega l'artista non trova pennelli o scalpelli, ma gli strumenti per costruirsi una reputazione concreta. Il collezionista vi trova quelli per riconoscerla.

**Organo cognitivo dell'ecosistema FlorenceEGI** dedicato allo sviluppo professionale dell'artista come brand e alla valutazione informata da parte del collezionista.

## Stack

- **Backend**: Laravel 12 (PHP 8.3) + Sanctum SSO + Spatie RBAC
- **Frontend**: React 19 + TypeScript + Tailwind + Vite
- **Database**: PostgreSQL RDS (schema `bottega.*`) — condiviso ecosistema `florenceegi`
- **AI**: Claude (Maestro di Bottega) + RAG Creator/Collector
- **Blockchain**: Algorand via EGI (CoA Sigillo, Mint/Bind)

## Architettura

La Bottega e un organo esterno che comunica con l'ecosistema via API:
- **Consuma**: EGI (NPE, Council, Traits, CoA, Collections, Opere, Mint, Egili), EGI-Credential (wallet, verification), NATAN_LOC (skill extraction)
- **Espone**: Maestro AI, strumenti diagnostici (Microscopio, Sestante), Valuator

## URL

- **Produzione**: `la-bottega.florenceegi.com`
- **SSOT documentazione**: `EGI-DOC/docs/la-bottega/`

---

*FlorenceEGI S.r.l. — Firenze, Italia*
*Oracode OS3.0*
