<?php

declare(strict_types=1);

/**
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Seed iniziale RAG Creator — knowledge base fondativa per il Maestro di Bottega.
 *          Documenti curati su pricing, marketing, bio, mercato, narrative, presenza digitale.
 *          Eseguire via: php artisan db:seed --class=RagCreatorSeeder
 *          NOTA: richiede OPENAI_API_KEY per generazione embeddings.
 */

namespace Database\Seeders;

use App\Services\Rag\RagCreatorIndexer;
use Illuminate\Database\Seeder;

class RagCreatorSeeder extends Seeder
{
    public function run(RagCreatorIndexer $indexer): void
    {
        $documents = $this->getSeedDocuments();

        $this->command->info("Indexing " . count($documents) . " seed documents...");

        $result = $indexer->indexBatch($documents);

        $this->command->info("Indexed: {$result['indexed']} | Failed: {$result['failed']}");

        foreach ($result['results'] as $r) {
            $this->command->line("  ✓ {$r['document_id']} ({$r['chunks_count']} chunks)");
        }
    }

    private function getSeedDocuments(): array
    {
        return [

            // ── PRICING LOGIC ──────────────────────────────────────────────
            [
                'title' => 'Strategie di pricing per artisti emergenti',
                'category' => 'pricing_logic',
                'target_career_level' => 'emerging',
                'target_percorso' => 'zero',
                'tags' => ['pricing', 'emergenti', 'primo-prezzo'],
                'raw_text' => <<<'TEXT'
Stabilire il primo prezzo per un'opera è una delle decisioni più importanti per un artista emergente. Non si tratta di attribuire un valore emotivo, ma di posizionarsi in un mercato reale.

Regola fondamentale: il prezzo deve poter solo salire. Partire troppo alti significa non vendere e doversi svalutare. Partire calibrati significa costruire una traiettoria ascendente credibile.

Formula base per il primo prezzo:
Costo materiali + (ore lavoro × tariffa oraria ragionevole) + margine 20-40% = prezzo base.
La tariffa oraria per un emergente in Italia si colloca tra 15-25€/ora.

Fattori di aggiustamento:
- Medium: olio su tela ha un premium percepito rispetto ad acrilico. Scultura e installazione hanno costi materiali più alti.
- Dimensione: il prezzo al cm² è il benchmark più usato nelle gallerie. Calcolare il proprio prezzo/cm² e mantenerlo coerente.
- Edizioni (stampe, fotografia): il prezzo unitario scende con la tiratura. Edizione 1/5 vale circa 3x rispetto a 1/50.
- Geografia: Milano e Roma hanno un mercato con prezzi medi 30-50% superiori a città più piccole.

Errori comuni:
1. Regalare opere per "farsi conoscere" — distrugge il valore percepito
2. Prezzi diversi per amici vs sconosciuti — crea incoerenza
3. Non tenere un registro prezzi — impossibile dimostrare crescita
4. Sconti superiori al 10% — segnale di debolezza

Strategia progressiva:
Primi 6 mesi: prezzo entry-level, obiettivo 10-15 vendite per validare il mercato.
6-18 mesi: incremento 10-15% se le vendite sono costanti.
Dopo 18 mesi: rivalutazione basata su CV espositivo, vendite, collezioni.
TEXT,
            ],

            // ── MARKETING GUIDE ────────────────────────────────────────────
            [
                'title' => 'Social media strategy per artisti visivi',
                'category' => 'marketing_guide',
                'target_career_level' => 'all',
                'target_percorso' => 'crescita',
                'tags' => ['social-media', 'instagram', 'strategia', 'contenuti'],
                'raw_text' => <<<'TEXT'
I social media sono lo strumento più accessibile per un artista per costruire un pubblico. Ma la maggior parte degli artisti li usa male: pubblica opere finite senza contesto, senza strategia, senza continuità.

Principio chiave: le persone comprano storie, non oggetti. Ogni post deve raccontare qualcosa.

Struttura contenuti settimanale consigliata (Instagram):
- Lunedì: Opera finita con testo narrativo (perché l'hai creata, cosa significa)
- Mercoledì: Processo / work in progress (video breve, time-lapse, dettaglio)
- Venerdì: Dietro le quinte / vita in studio / riflessione personale
- Storie: 3-5 al giorno nei giorni di attività, rispondi sempre ai messaggi

Regole di engagement:
1. Rispondi a OGNI commento nelle prime 2 ore — l'algoritmo premia l'interazione
2. Commenta autenticamente su 10-15 profili di artisti/gallerie/collezionisti al giorno
3. Usa 20-25 hashtag pertinenti (mix: 5 grandi >500k, 10 medi 50-500k, 10 di nicchia <50k)
4. Posta tra le 18:00 e le 21:00 (ora italiana) per massimizzare la visibilità

Errori fatali:
- Pubblicare solo opere senza contesto → profilo da catalogo, zero connessione emotiva
- Sparire per settimane → l'algoritmo ti penalizza, il pubblico ti dimentica
- Comprare follower → distrugge il tasso di engagement, le gallerie controllano
- Ignorare i DM → i collezionisti scrivono in DM, non via email

Metriche che contano:
- Tasso di engagement (like+commenti/follower) > 3% = buono, > 5% = eccellente
- Salvataggi: più importanti dei like — indicano interesse d'acquisto
- Messaggi ricevuti: segnale diretto di interesse
- Click sul link in bio: conversioni verso portfolio o shop
TEXT,
            ],

            // ── BIO WRITING ────────────────────────────────────────────────
            [
                'title' => 'Come scrivere una bio artista efficace',
                'category' => 'bio_writing',
                'target_career_level' => 'all',
                'target_percorso' => 'all',
                'tags' => ['bio', 'artist-statement', 'scrittura', 'presentazione'],
                'raw_text' => <<<'TEXT'
La bio è il testo più letto di un artista. Appare ovunque: sito, social, cataloghi, candidature, gallerie. Eppure la maggior parte delle bio sono scritte male: troppo lunghe, troppo vaghe, troppo accademiche.

Struttura della bio efficace (3 paragrafi, max 200 parole):

Paragrafo 1 — Chi sei:
Nome, dove vivi/lavori, medium principale. Una frase che cattura l'essenza del tuo lavoro.
Esempio: "Maria Rossi è un'artista visiva basata a Firenze che esplora la memoria collettiva attraverso installazioni tessili e fotografia analogica."

Paragrafo 2 — Il tuo percorso:
Formazione rilevante (solo se aggiunge credibilità), mostre significative (max 3-4), riconoscimenti.
NON elencare tutto. Seleziona ciò che costruisce la narrativa.

Paragrafo 3 — La tua visione:
Cosa guida il tuo lavoro. Quale domanda esplori. Perché il tuo lavoro è rilevante oggi.
Questo è il paragrafo che fa la differenza tra una bio dimenticabile e una memorabile.

Versioni da mantenere:
- Bio lunga (200 parole): per sito, cataloghi, candidature
- Bio media (80 parole): per social media, comunicati stampa
- Bio breve (30 parole): per didascalie, menzioni rapide

Artist Statement vs Bio:
La bio parla di te in terza persona. L'artist statement parla del tuo lavoro in prima persona.
L'artist statement risponde a: Cosa fai? Perché lo fai? Come lo fai?
Max 150 parole. Linguaggio accessibile. Zero gergo accademico gratuito.

Errori comuni:
1. Iniziare con la data di nascita — nessuno se ne interessa
2. Elencare 30 mostre — è un CV, non una bio
3. Usare "il mio lavoro esplora le intersezioni tra..." — cliché vuoto
4. Scrivere in prima persona la bio (o in terza persona lo statement) — convenzione violata
5. Non aggiornarla — una bio del 2020 nel 2026 comunica disinteresse
TEXT,
            ],

            // ── MARKET ANALYSIS ────────────────────────────────────────────
            [
                'title' => 'Il mercato dell\'arte contemporanea in Italia: dati e trend 2024-2026',
                'category' => 'market_analysis',
                'target_career_level' => 'all',
                'target_percorso' => 'mercato',
                'tags' => ['mercato', 'italia', 'trend', 'dati', 'contemporanea'],
                'raw_text' => <<<'TEXT'
Il mercato dell'arte contemporanea italiano è in una fase di trasformazione strutturale. I dati degli ultimi tre anni mostrano tendenze chiare che ogni artista dovrebbe conoscere.

Dimensioni del mercato:
Il mercato dell'arte globale vale circa 65 miliardi di dollari (Art Basel/UBS Report 2024). L'Italia rappresenta circa il 3% del mercato globale, con un valore stimato di 2 miliardi.

Segmenti di prezzo e distribuzione:
- Under 5.000€: 60% delle transazioni (volume), 8% del valore
- 5.000-50.000€: 25% delle transazioni, 22% del valore
- Over 50.000€: 15% delle transazioni, 70% del valore

Per un artista emergente, il segmento under 5.000€ è il mercato di riferimento. La competizione è alta ma il volume di transazioni è significativo.

Canali di vendita in Italia:
1. Gallerie: ancora il canale principale (45% delle vendite). Commissione media 50%.
2. Fiere: Artissima (Torino), miart (Milano), Arte Fiera (Bologna) — i tre appuntamenti chiave.
3. Online: in crescita costante, ora circa 20% del mercato. Piattaforme: Artsy, Saatchi Art, e marketplace locali.
4. Studio sales: vendite dirette, in crescita post-pandemia (15%).
5. Aste: principalmente per artisti affermati, ma le aste online stanno abbassando la soglia d'ingresso.

Trend emergenti 2024-2026:
- Art+Tech: crescente interesse per opere che integrano tecnologia (non solo NFT, ma AR, AI-assisted, bioarte)
- Sostenibilità: collezionisti sempre più attenti a materiali e processi sostenibili
- Collezionismo giovane (25-40): preferisce acquisti online, cerca connessione diretta con l'artista
- Decentramento: crescita di scene artistiche fuori da Milano/Roma (Torino, Bologna, Firenze, Napoli)
- Micro-collezionismo: edizioni limitate, stampe fine art, piccoli formati — democratizzazione dell'acquisto

Medium più richiesti (in ordine):
1. Pittura (rimane dominante, 40% del mercato)
2. Fotografia (in crescita, 18%)
3. Scultura (stabile, 12%)
4. Arte digitale / new media (in forte crescita, 10%)
5. Ceramica / fiber art (trend in ascesa, 8%)
TEXT,
            ],

            // ── CASE STUDY ─────────────────────────────────────────────────
            [
                'title' => 'Caso studio: da zero a 50 vendite in 18 mesi — strategia completa',
                'category' => 'case_study',
                'target_career_level' => 'emerging',
                'target_percorso' => 'zero',
                'tags' => ['caso-studio', 'emergente', 'strategia', 'vendite', 'crescita'],
                'raw_text' => <<<'TEXT'
Profilo: artista visiva, 28 anni, pittrice (acrilico su tela e carta), Firenze. Nessuna formazione accademica in arte. Lavoro full-time non correlato. Zero vendite, zero mostre, 200 follower Instagram.

Mese 1-3: Fondamenta
- Definizione del proprio stile: 3 mesi di produzione intensiva (weekends), 40 opere prodotte, 12 selezionate come portfolio iniziale
- Fotografia professionale delle opere (investimento: 300€ per un servizio fotografico dedicato)
- Creazione profilo Instagram ottimizzato: bio chiara, link in bio con portfolio, feed coerente
- Apertura profilo Artsy (gratuito per artisti) e Saatchi Art
- Pricing: formula costo+tempo+margine → range 150-600€ per opera
- Prima bio scritta (3 versioni)

Mese 4-6: Visibilità
- Posting regolare Instagram: 3 volte/settimana (opera, processo, personale)
- Candidatura a 15 open call (3 accettate)
- Prima mostra collettiva in uno spazio indipendente (costo partecipazione: 150€)
- Prime 5 vendite: 3 tramite Instagram DM, 2 tramite Saatchi Art
- Revenue totale: 1.800€ | Follower: 1.200

Mese 7-12: Trazione
- Seconda e terza mostra (una galleria emergente, una collettiva in fiera satellite)
- Lancio newsletter mensile (Mailchimp gratuito) — 80 iscritti dal sito
- Collaborazione con 2 artisti per mostra a quattro mani → esposizione ai loro network
- Vendite: 20 opere | Revenue: 7.500€ | Follower: 3.800
- Primo incremento prezzi: +15% su tutta la produzione

Mese 13-18: Consolidamento
- Rappresentanza informale da piccola galleria fiorentina (no contratto esclusiva)
- Partecipazione a fiera satellite con la galleria (5 opere esposte, 3 vendute)
- Articolo su blog d'arte locale → credibilità e backlink per SEO
- Apertura partita IVA regime forfettario (superata soglia hobby)
- Vendite totali: 50 | Revenue cumulativo: 18.000€ | Follower: 6.500
- Pricing medio: da 250€ a 450€ per opera (+80% in 18 mesi)

Lezioni chiave:
1. La coerenza batte il talento: postare regolarmente e produrre costantemente conta più del singolo capolavoro
2. Le prime vendite vengono dal network personale esteso — non vergognarsi di condividere
3. Le open call sono il modo più accessibile per costruire un CV espositivo
4. Il prezzo iniziale basso non è una sconfitta: è una strategia di penetrazione
5. Documentare il processo attrae più engagement delle opere finite
TEXT,
            ],

            // ── OPPORTUNITY ────────────────────────────────────────────────
            [
                'title' => 'Guida completa alle opportunità per artisti in Italia',
                'category' => 'opportunity',
                'target_career_level' => 'all',
                'target_percorso' => 'all',
                'tags' => ['opportunità', 'open-call', 'residenze', 'bandi', 'premi'],
                'raw_text' => <<<'TEXT'
Navigare il mondo delle opportunità è una competenza fondamentale per un artista. Esistono centinaia di bandi, premi, residenze e call ogni anno solo in Italia. La chiave è sapere dove cercare e come candidarsi efficacemente.

Tipologie di opportunità:

1. OPEN CALL per mostre
Cosa sono: inviti aperti a tutti per sottoporre opere a una selezione curatoriale.
Dove trovarle: Artconnect.com, Wooloo.org, E-flux.com, Instagram (hashtag #opencall #callforartists)
Costo: spesso gratuite, alcune chiedono 10-30€ di fee di selezione.
Tasso di accettazione medio: 5-15%. Candidarsi ad almeno 3-4 al mese.

2. RESIDENZE ARTISTICHE
Cosa sono: periodi (2-12 settimane) in cui un artista lavora in uno spazio dedicato, spesso in un contesto nuovo.
In Italia: Fondazione Spinola Banna (Torino), MACRO Asilo (Roma), Viafarini (Milano), Bocs Art (Cosenza).
Internazionali accessibili: Cité des Arts (Parigi), ISCP (New York), Gasworks (Londra).
Copertura: le migliori coprono viaggio, alloggio e studio. Le meno note chiedono una fee (300-1500€).

3. PREMI E CONCORSI
Premi italiani rilevanti: Premio Cairo, Premio Terna, Premio Lissone, Premio Combat.
Strategia: candidarsi ai premi "mid-tier" con meno competizione piuttosto che solo ai grandi nomi.
Il valore non è solo economico: un premio sul CV ha un effetto moltiplicatore sulla credibilità.

4. BANDI PUBBLICI
Comuni, Regioni e Fondazioni bancarie pubblicano bandi per interventi artistici, arte pubblica, eventi culturali.
Dove trovarli: siti delle Fondazioni (Compagnia di San Paolo, Fondazione Cariplo), bandi regionali cultura.
Complessità: richiedono spesso progetti dettagliati e budget preventivi — collaborare con un curatore aiuta.

5. FIERE
Le tre fiere principali italiane: Artissima (Torino, novembre), miart (Milano, aprile), Arte Fiera (Bologna, febbraio).
Fiere satellite e emergenti: più accessibili, costi minori, pubblico curioso.
Un artista emergente può partecipare tramite una galleria o in sezioni dedicate ai giovani.

Come candidarsi efficacemente:
- Portfolio aggiornato e coerente (12-20 opere, fotografate professionalmente)
- Bio e statement pronti in italiano e inglese
- Lettera motivazionale specifica per ogni candidatura (mai generica)
- Budget realistico se richiesto
- Rispettare SEMPRE le deadline e i formati richiesti
TEXT,
            ],

            // ── NARRATIVE STRATEGY ─────────────────────────────────────────
            [
                'title' => 'Costruire una narrativa artistica vincente',
                'category' => 'narrative_strategy',
                'target_career_level' => 'all',
                'target_percorso' => 'crescita',
                'tags' => ['narrativa', 'storytelling', 'portfolio', 'presentazione', 'curatore'],
                'raw_text' => <<<'TEXT'
Un artista senza narrativa è invisibile. La narrativa non è marketing — è il tessuto connettivo che dà senso al lavoro e lo rende memorabile. Gallerie, curatori e collezionisti investono in storie, non in singole opere.

I 4 pilastri della narrativa artistica:

1. IL PERCHÉ (Motivazione)
Perché fai quello che fai? Non "perché mi piace dipingere" — ma cosa ti spinge veramente.
La risposta autentica a questa domanda è il cuore della tua narrativa.
Esercizio: scrivi 500 parole su cosa ti tiene sveglio la notte come artista. Il nucleo è lì.

2. IL COME (Processo)
Il tuo processo è unico quanto la tua opera. Documentalo, raccontalo, rendilo parte dell'opera stessa.
Un collezionista che conosce il processo sente una connessione più profonda con l'opera.
Il "come" include: scelta dei materiali, rituali di studio, ricerca, iterazione.

3. IL COSA (Temi)
I temi ricorrenti nel tuo lavoro definiscono il territorio che presidi.
Non serve un tema unico — servono temi coerenti che si intersecano.
Esempio: memoria, paesaggio industriale, corpo femminile — tre temi che insieme creano un territorio riconoscibile.

4. IL DOVE (Contesto)
In quale conversazione artistica e culturale si inserisce il tuo lavoro?
Non devi per forza citare movimenti o artisti — ma sapere dove ti posizioni aiuta gli altri a comprenderti.

Come costruire la narrativa in pratica:

Portfolio narrativo (vs portfolio catalogo):
- Raggruppa le opere per serie/progetto, non per data
- Ogni serie ha un titolo, un testo introduttivo (50-80 parole), e 5-8 opere selezionate
- L'ordine racconta una progressione: dalla serie più recente alla più fondativa
- Include 2-3 immagini di processo/studio tra le serie

Presentazione a un curatore (5 minuti):
1. Chi sei e cosa fai (30 secondi — la tua bio breve)
2. Il progetto attuale (2 minuti — mostra 3-4 immagini chiave)
3. Perché questo progetto ora (1 minuto — il contesto)
4. Cosa vuoi fare dopo (1 minuto — la visione)
5. Domanda aperta (30 secondi — coinvolgi il curatore)

Errori narrativi:
- Cambiare stile ogni 6 mesi → nessuna riconoscibilità
- Narrativa troppo personale/terapeutica → il pubblico non riesce a connettersi
- Zero narrativa → l'opera resta decorazione
- Narrativa accademica incomprensibile → esclude il 90% del pubblico potenziale
TEXT,
            ],

            // ── DIGITAL PRESENCE ───────────────────────────────────────────
            [
                'title' => 'Presenza digitale professionale per artisti',
                'category' => 'digital_presence',
                'target_career_level' => 'all',
                'target_percorso' => 'crescita',
                'tags' => ['sito', 'portfolio-online', 'seo', 'newsletter', 'digitale'],
                'raw_text' => <<<'TEXT'
Nel 2026, un artista senza presenza digitale professionale non esiste per il mercato. Il sito web è il biglietto da visita permanente, i social sono l'amplificatore, la newsletter è il canale di conversione.

IL SITO WEB DELL'ARTISTA

Requisiti minimi:
- Dominio proprio (nome.cognome.com o nome-cognome.art) — mai un sottodominio di piattaforma
- Portfolio con immagini ad alta risoluzione, titoli, dimensioni, medium, anno, prezzo (se in vendita)
- Bio aggiornata
- Pagina contatti con form funzionante
- Mobile responsive (60%+ del traffico è da mobile)
- Veloce (< 3 secondi di caricamento)

Piattaforme consigliate:
- Cargo.site: il più usato dagli artisti, minimal, elegante
- Format.com: specifico per portfolio creativi
- Squarespace: più commerciale ma ottimo per chi vende direttamente
- WordPress + tema portfolio: massima flessibilità, richiede più competenza tecnica

SEO per artisti (minimo vitale):
- Title tag: "Nome Cognome — Medium — Città" (es. "Maria Rossi — Pittrice — Firenze")
- Alt text su ogni immagine: "Titolo opera, medium, dimensioni, anno — Nome Cognome"
- Una pagina "About" con testo ricco di parole chiave naturali
- Google Business Profile se hai uno studio visitabile

LA NEWSLETTER

Perché è fondamentale: è l'unico canale che possiedi. Instagram può cambiare l'algoritmo domani. La tua mailing list è tua per sempre.

Frequenza: mensile è il minimo. Bisettimanale è l'ottimo.

Contenuto tipo:
- 1 opera nuova o progetto in corso
- 1 riflessione personale o dietro le quinte
- 1 notizia (mostra in arrivo, pubblicazione, open studio)
- Call to action chiara (visita la mostra, rispondi a questa email, acquista)

Strumenti: Mailchimp (gratuito fino a 500 contatti), Substack (gratuito, ottime funzionalità social), Buttondown (minimal, pro-privacy).

Come costruire la lista:
- Form di iscrizione sul sito (popup NON invasivo o footer)
- Registro email durante mostre e open studio
- Link in bio su Instagram
- Offerta di valore: "Iscriviti e ricevi il mio studio journal mensile"
TEXT,
            ],
        ];
    }
}
