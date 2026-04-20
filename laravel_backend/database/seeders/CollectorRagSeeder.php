<?php

declare(strict_types=1);

/**
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-20
 * @purpose Seeder base RAG Collector — 9 documenti educativi iniziali (3 per categoria).
 *          Glossario mercato arte + Etica collezionismo + Storia artisti FlorenceEGI.
 *          Contenuti inline, zero copyright. Invoca RagCollectorIndexer per embedding reali.
 *          MAI promozionali: voce oggettiva per il collezionista.
 */

namespace Database\Seeders;

use App\Services\Rag\RagCollectorIndexer;
use Illuminate\Database\Seeder;

class CollectorRagSeeder extends Seeder
{
    public function __construct(
        private readonly RagCollectorIndexer $indexer,
    ) {}

    public function run(): void
    {
        $documents = array_merge(
            $this->glossarioMercato(),
            $this->eticaCollezionismo(),
            $this->storiaArtistiEgi(),
        );

        $result = $this->indexer->indexBatch($documents);

        $this->command->info(sprintf(
            'Collector RAG seed complete — indexed: %d, failed: %d',
            $result['indexed'],
            $result['failed']
        ));
    }

    /**
     * 3 documenti categoria glossario_mercato.
     */
    private function glossarioMercato(): array
    {
        return [
            [
                'document_id' => 'col_glossario_provenance',
                'title' => 'Provenance — la catena di proprietà di un\'opera',
                'category' => 'glossario_mercato',
                'subcategory' => 'autenticazione',
                'tags' => ['provenance', 'autenticazione', 'catena-proprieta'],
                'language' => 'it',
                'target_expertise_level' => 'beginner',
                'target_collector_type' => 'all',
                'target_medium_interest' => 'all',
                'price_bracket' => 'all',
                'source_type' => 'internal',
                'raw_text' => "La provenance (o provenienza) è la ricostruzione documentata della catena di possesso di un'opera d'arte, dal momento della creazione fino al collezionista attuale.\n\nUna provenance solida include: atti di vendita, ricevute di gallerie, cataloghi di mostre in cui l'opera è stata esposta, documenti di restituzione (se applicabili), lasciti testamentari, corrispondenza dell'artista. Ogni passaggio di proprietà dovrebbe essere verificabile.\n\nPerché conta per il collezionista: una provenance rigorosa aumenta il valore dell'opera, ne certifica l'autenticità e protegge da rischi legali. Opere senza provenance documentata — specialmente se risalenti a periodi di confisca storica (1933–1945) o a scavi archeologici non autorizzati — possono essere oggetto di richieste di restituzione.\n\nSegnali di allarme: assenza di documenti per periodi lunghi, discrepanze nei proprietari dichiarati, mancanza di cataloghi che citano l'opera, firme troppo recenti su documenti presunti storici.\n\nPer l'arte contemporanea digitale (come gli EGI di FlorenceEGI), la provenance è garantita nativamente dalla blockchain Algorand: ogni trasferimento di proprietà è registrato in modo immutabile e pubblicamente verificabile. Questo elimina l'ambiguità tipica della provenance cartacea.",
            ],
            [
                'document_id' => 'col_glossario_edizione_tiratura',
                'title' => 'Edizione e tiratura — leggere i numeri su stampe e multipli',
                'category' => 'glossario_mercato',
                'subcategory' => 'edizioni',
                'tags' => ['edizione', 'tiratura', 'stampa', 'multiplo', 'prova-artista'],
                'language' => 'it',
                'target_expertise_level' => 'beginner',
                'target_collector_type' => 'all',
                'target_medium_interest' => 'all',
                'price_bracket' => 'entry',
                'source_type' => 'internal',
                'raw_text' => "Quando un'opera è prodotta in più esemplari (incisioni, litografie, fotografie, bronzi, arte digitale), si parla di edizione. La tiratura è il numero totale di esemplari prodotti.\n\nNotazione standard: un numero come 12/50 significa che l'esemplare è il dodicesimo di una serie di 50. Più la tiratura è bassa, maggiore è la rarità e — a parità di altri fattori — il valore.\n\nOltre agli esemplari numerati esistono:\n- P.A. (prova d'artista) o A.P. (artist's proof): copie riservate all'artista, solitamente 10% della tiratura\n- E.A. (épreuve d'artiste): equivalente francese\n- H.C. (hors commerce): fuori commercio, per musei o donazioni\n- P.P. (printer's proof): riservate allo stampatore\n- B.A.T. (bon à tirer): la prova approvata che fissa lo standard della tiratura\n\nPer un collezionista è importante: verificare che la tiratura dichiarata sia reale, che la matrice sia stata distrutta o marcata al termine della stampa, che non esistano edizioni successive non dichiarate.\n\nNell'arte digitale come gli EGI, la tiratura è codificata sulla blockchain al momento della creazione: ogni token rappresenta un esemplare numerato, la serie non è modificabile, nuovi token non possono essere coniati dopo la chiusura. Questa trasparenza tecnica sostituisce il lavoro tradizionale del perito.",
            ],
            [
                'document_id' => 'col_glossario_condition_report',
                'title' => 'Condition report — cosa guardare nella scheda di condizione',
                'category' => 'glossario_mercato',
                'subcategory' => 'stato-conservazione',
                'tags' => ['condition-report', 'conservazione', 'restauro'],
                'language' => 'it',
                'target_expertise_level' => 'intermediate',
                'target_collector_type' => 'all',
                'target_medium_interest' => 'all',
                'price_bracket' => 'mid',
                'source_type' => 'internal',
                'raw_text' => "Il condition report è la scheda tecnica che descrive lo stato di conservazione di un'opera al momento della vendita. È un documento critico: vizi non dichiarati sono motivo di contestazione e, in molte giurisdizioni, di recesso.\n\nUn condition report serio include:\n- Ispezione visiva complessiva (a luce naturale e a luce radente)\n- Ispezione ai raggi ultravioletti (rivela ritocchi recenti, vernici)\n- Dettagli costruttivi: supporto, medium, strato pittorico, vernici\n- Mappa delle lacune, cadute di colore, craquelure, strappi, fori\n- Interventi di restauro pregressi — datati se possibile\n- Stato della cornice (se originale o successiva)\n- Giudizio sullo stato: da \"eccellente\" a \"richiede restauro\"\n\nPer collezionisti: leggere il condition report prima di impegnarsi all'acquisto. Richiedere immagini ad alta risoluzione di tutti i dettagli menzionati. Per acquisti significativi, commissionare un perito indipendente.\n\nOpere contemporanee digitali: il concetto di condition report si sposta. La conservazione fisica è sostituita dalla sicurezza crittografica del token e dalla preservazione del file sorgente. Per gli EGI, il file originale è archiviato su storage decentralizzato (IPFS) con hash crittografico registrato on-chain — una forma di condition report nativa.",
            ],
        ];
    }

    /**
     * 3 documenti categoria etica_collezionismo.
     */
    private function eticaCollezionismo(): array
    {
        return [
            [
                'document_id' => 'col_etica_resale_right',
                'title' => 'Diritto di seguito (resale right) — cosa implica per il collezionista',
                'category' => 'etica_collezionismo',
                'subcategory' => 'diritti-artista',
                'tags' => ['resale-right', 'droit-de-suite', 'diritti-artista'],
                'language' => 'it',
                'target_expertise_level' => 'intermediate',
                'target_collector_type' => 'all',
                'target_medium_interest' => 'all',
                'price_bracket' => 'all',
                'source_type' => 'internal',
                'raw_text' => "Il diritto di seguito (in Francia droit de suite, in UE resale right) è il diritto dell'artista — o dei suoi eredi per 70 anni dopo la morte — a percepire una percentuale sul prezzo di ogni rivendita successiva della sua opera, quando questa avviene attraverso un operatore professionale (casa d'aste, galleria, mercante).\n\nNell'Unione Europea è armonizzato dalla Direttiva 2001/84/CE. In Italia è disciplinato dal D.Lgs. 118/2006. La percentuale è progressiva e decresce con il crescere del prezzo: in media 4% per prezzi tra 3.000 e 50.000 €, scendendo fino allo 0,25% per prezzi superiori a 500.000 €, con un tetto di 12.500 € per transazione.\n\nChi paga: tecnicamente il venditore, pratiche di mercato spesso trasferiscono l'onere. L'SIAE (in Italia) raccoglie e distribuisce.\n\nImplicazioni per il collezionista:\n- È un costo accessorio da considerare nel business case di rivendita\n- È una forma di riconoscimento del lavoro continuativo dell'artista e della sua famiglia\n- Non si applica alle vendite private tra collezionisti\n- Non si applica ai primi acquisti (solo alle rivendite)\n\nNell'arte digitale basata su blockchain, il resale right può essere codificato nello smart contract: ogni trasferimento di token trasferisce automaticamente una percentuale all'artista. È una delle innovazioni più coerenti con lo spirito della direttiva europea.",
            ],
            [
                'document_id' => 'col_etica_provenance_restituzioni',
                'title' => 'Provenance e restituzioni — la responsabilità del collezionista informato',
                'category' => 'etica_collezionismo',
                'subcategory' => 'restituzioni',
                'tags' => ['provenance', 'restituzioni', 'nazi-loot', 'archeologia'],
                'language' => 'it',
                'target_expertise_level' => 'advanced',
                'target_collector_type' => 'all',
                'target_medium_interest' => 'all',
                'price_bracket' => 'high',
                'source_type' => 'internal',
                'raw_text' => "Due aree di rischio restituzione dominano il dibattito etico del collezionismo: le opere sottratte durante il regime nazista (1933–1945) e i reperti archeologici di provenienza illecita.\n\nOpere di era nazista: i Principi di Washington (1998) hanno stabilito l'obbligo morale di identificare le opere sottratte e di raggiungere \"soluzioni giuste ed eque\" con gli eredi delle vittime. Molti paesi hanno istituito commissioni di ricerca (Commissione Mondialità in Francia, Limbach Commission in Germania). Il collezionista che acquista un'opera con una lacuna di provenance nel periodo 1933–1945 si espone a richieste di restituzione anche a decenni di distanza.\n\nReperti archeologici: la Convenzione UNESCO del 1970 e la Convenzione UNIDROIT del 1995 stabiliscono obblighi di restituzione per beni culturali esportati illegalmente. Molti paesi (Italia, Egitto, Grecia, Turchia) hanno politiche attive di recupero. Acquistare reperti senza documentazione pre-1970 è rischioso e spesso illegale.\n\nPratica di due diligence per il collezionista:\n- Richiedere sempre la provenance completa\n- Consultare database pubblici: Art Loss Register, Interpol Works of Art, database museali di rivendicazioni\n- Per acquisti significativi, commissionare ricerca indipendente\n- In caso di dubbio: non acquistare\n\nIl collezionismo è anche un atto di responsabilità storica. L'opera entra nella propria vita, ma conserva la propria memoria — che il collezionista ereditato e trasmesso.",
            ],
            [
                'document_id' => 'col_etica_sostenibilita',
                'title' => 'Sostenibilità del collezionismo — impatto ambientale e culturale',
                'category' => 'etica_collezionismo',
                'subcategory' => 'sostenibilita',
                'tags' => ['sostenibilita', 'ambiente', 'clima', 'digitale'],
                'language' => 'it',
                'target_expertise_level' => 'intermediate',
                'target_collector_type' => 'all',
                'target_medium_interest' => 'all',
                'price_bracket' => 'all',
                'source_type' => 'internal',
                'raw_text' => "Il collezionismo d'arte ha un'impronta materiale spesso trascurata: materiali di produzione, trasporti internazionali, condizionamento ambientale per la conservazione, imballaggi protettivi, fiere d'arte globali con alta intensità di viaggio.\n\nGallery Climate Coalition (GCC), nata nel 2020, ha stimato che un singolo trasporto internazionale di un'opera di medie dimensioni può generare 200–500 kg di CO2e. Una fiera d'arte globale tipo Art Basel genera centinaia di tonnellate di CO2e in trasporti e logistica.\n\nScelte consapevoli per il collezionista:\n- Privilegiare artisti della propria area geografica quando possibile\n- Chiedere al venditore opzioni di trasporto a minor impatto (nave invece di aereo per viaggi lunghi)\n- Valutare imballaggi riutilizzabili e riciclabili\n- Preferire gallerie e fiere che hanno firmato il protocollo GCC\n- Per il digitale: verificare il consumo energetico della blockchain scelta\n\nIl caso dell'arte digitale: le prime critiche alle NFT (2020–2021) riguardavano Ethereum proof-of-work, con consumi energetici elevati. Il passaggio a proof-of-stake ha ridotto i consumi del 99,95%. Blockchain come Algorand (usata da FlorenceEGI per gli EGI) sono carbon-negative: compensano più CO2 di quanto emettono.\n\nCollezionare non è solo possedere. È anche scegliere come quella scelta impatta sul mondo fisico — e quale pratica si normalizza per chi verrà dopo.",
            ],
        ];
    }

    /**
     * 3 documenti categoria storia_artisti_egi.
     */
    private function storiaArtistiEgi(): array
    {
        return [
            [
                'document_id' => 'col_storia_emergere_artisti_egi',
                'title' => 'Come emergono gli artisti FlorenceEGI — il percorso dell\'emerging creator',
                'category' => 'storia_artisti_egi',
                'subcategory' => 'percorso-emergenti',
                'tags' => ['emerging', 'percorso-artista', 'carriera'],
                'language' => 'it',
                'target_expertise_level' => 'all',
                'target_collector_type' => 'emerging',
                'target_medium_interest' => 'all',
                'price_bracket' => 'entry',
                'source_type' => 'internal',
                'raw_text' => "Gli artisti che emergono nella piattaforma FlorenceEGI seguono un percorso strutturato in tre fasi, fondato sulla Bottega — uno spazio di strumenti oggettivi a supporto della crescita.\n\nFase Zero: l'artista entra con identità minima, senza storico di vendite. La Bottega gli fornisce gli strumenti fondazionali: Microscopio (analisi opera singola), Binocolo (scansione opportunità), Maestro (chat AI su marketing e pricing). L'obiettivo è la prima pubblicazione coerente di una piccola serie.\n\nFase Crescita: l'artista ha pubblicato 5–20 opere, ha ricevuto i primi acquisti. Entrano in gioco Market Pulse (lettura di mercato per il suo medium), Visibility Tracker (misurazione della presenza), Price Advisor (calibrazione prezzi). L'obiettivo è la costruzione di un collector di nicchia.\n\nFase Mercato: l'artista ha una base collector stabile, pubblica con regolarità, ha metriche di vendita consolidate. Gli strumenti si spostano su Sestante (direzione strategica), analisi comparate con artisti simili, pianificazione pluriennale.\n\nPer il collezionista: la trasparenza del percorso è un segnale. Gli artisti FlorenceEGI hanno un ID pubblico delle fasi, dei traguardi, dei numeri di opere. Acquistare da un artista in Fase Zero significa sostenere il percorso iniziale — con il rischio e il potenziale tipici della fase; acquistare in Fase Mercato significa entrare in un segmento già stabilizzato.\n\nNessun giudizio di merito: fasi diverse corrispondono a logiche diverse di acquisto. La Bottega lato collezionista aiuta a leggere la fase dell'artista e a scegliere coerentemente con il proprio obiettivo.",
            ],
            [
                'document_id' => 'col_storia_pattern_stilistici_egi',
                'title' => 'Pattern stilistici dei creator FlorenceEGI — leggere la coerenza di un corpus',
                'category' => 'storia_artisti_egi',
                'subcategory' => 'analisi-stilistica',
                'tags' => ['stile', 'coerenza', 'corpus', 'lettura-opera'],
                'language' => 'it',
                'target_expertise_level' => 'intermediate',
                'target_collector_type' => 'all',
                'target_medium_interest' => 'all',
                'price_bracket' => 'all',
                'source_type' => 'internal',
                'raw_text' => "Un aspetto che distingue il collezionismo informato da quello impulsivo è la capacità di leggere la coerenza interna del corpus di un artista. Non solo la qualità della singola opera, ma come le opere dialogano tra loro.\n\nPer gli artisti FlorenceEGI, quattro dimensioni aiutano a leggere la coerenza:\n\n1. Coerenza formale: palette, composizione, trattamento del medium, ricorrenze di segno. Un corpus coerente presenta una grammatica riconoscibile anche quando il soggetto cambia.\n\n2. Coerenza tematica: i soggetti ritornano, si trasformano, si approfondiscono. Un artista maturo torna su nuclei ossessivi — non si disperde su temi casuali.\n\n3. Coerenza evolutiva: il corpus mostra crescita, non ripetizione. Le opere del terzo anno dovrebbero differire dalle opere del primo anno — non solo tecnicamente, ma concettualmente.\n\n4. Coerenza di serie: l'artista pubblica in serie chiaramente identificabili, non come sequenza indistinta. Ogni serie ha un'unità interna e un ruolo nel corpus più ampio.\n\nQuesti criteri sono leggibili sulla pagina profilo artista di FlorenceEGI attraverso: la cronologia delle pubblicazioni, le etichette di serie, le descrizioni delle opere (quando curate dall'artista stesso), la metadata tecnica.\n\nUn collezionista informato dedica tempo a leggere il corpus prima di acquistare. Un'opera presa isolatamente può essere bella; inserita nel corpus, può rivelarsi un momento eccezionale o un passaggio meno significativo. La differenza non è qualitativa in senso assoluto — è relativa al progetto artistico.",
            ],
            [
                'document_id' => 'col_storia_percorso_evolutivo',
                'title' => 'Percorso evolutivo dell\'artista FlorenceEGI — cosa osservare nel tempo',
                'category' => 'storia_artisti_egi',
                'subcategory' => 'evoluzione-temporale',
                'tags' => ['evoluzione', 'crescita', 'tempo', 'carriera'],
                'language' => 'it',
                'target_expertise_level' => 'advanced',
                'target_collector_type' => 'established',
                'target_medium_interest' => 'all',
                'price_bracket' => 'all',
                'source_type' => 'internal',
                'raw_text' => "Il collezionismo di lunga durata non si esaurisce nell'acquisto. Si estende al seguire la traiettoria dell'artista nel tempo, al comprenderne le svolte, al calibrare l'aggiunta di nuove opere.\n\nSegnali di crescita sana in un artista FlorenceEGI:\n\n- Pubblicazioni regolari senza essere compulsive: in media 5–20 opere/anno per un emergente, 20–50 per un mid-career, pattern stabili per un established\n- Aumento progressivo della complessità: più serie sovrapposte, più ricerca nel medium, più ambizione formale\n- Traguardi oggettivi: inclusione in mostre collettive, review da terze parti, reportistica indipendente\n- Aggiornamenti narrativi dell'artista: bio, statement, descrizioni — segnali di maturazione concettuale\n\nSegnali da monitorare con cautela:\n\n- Pause lunghe senza spiegazione: possono indicare fase di ricerca o difficoltà personale\n- Cambi radicali di stile senza continuità: non sempre negativi, ma meritano lettura attenta\n- Aumento rapido dei prezzi senza corrispondente crescita oggettiva: rischio di bolla locale\n- Scomparsa da fiere/mostre o silenzio social prolungato\n\nStrumenti per il collezionista su La Bottega lato Collector:\n- Registro: portfolio tracking personale delle opere che possiede\n- Bilanciere: analisi di diversificazione della collezione\n- Portafoglio: valutazione longitudinale del patrimonio\n\nNessuno di questi strumenti predice il futuro — offrono invece una lente sul passato e sul presente che aiuta a formulare ipotesi consapevoli. Il collezionismo di qualità resta un atto di giudizio umano, non un algoritmo.",
            ],
        ];
    }
}
