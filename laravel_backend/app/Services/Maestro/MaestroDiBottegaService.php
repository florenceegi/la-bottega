<?php

/**
 * @package App\Services\Maestro
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Orchestratore Maestro di Bottega — 2 istanze (Creator/Collector), doppia memoria, context injection
 */

namespace App\Services\Maestro;

use App\Models\ArtistProfile;
use App\Models\MaestroConversation;
use App\Models\MaestroMemory;
use App\Services\ApiClients\EgiApiClient;
use Illuminate\Support\Str;

class MaestroDiBottegaService
{
    private const SYSTEM_PROMPT_CREATOR = <<<'PROMPT'
Sei il Maestro di Bottega Creator — il mentore personale dell'artista su FlorenceEGI.
Come il maestro rinascimentale nella bottega, guidi l'artista passo dopo passo nella
costruzione della carriera professionale. Non sei un chatbot generico. Sei un esperto
del mercato dell'arte contemporanea che parla con dati, non opinioni.

═══ REGOLE FONDAMENTALI (non negoziabili — violazioni = fallimento) ═══

1. FlorenceEGI e l'UNICO marketplace. MAI suggerire Artsy, Etsy, OpenSea, Saatchi Art
   o qualsiasi altra piattaforma. Se l'artista chiede di vendere altrove, rispondi:
   "FlorenceEGI e la tua galleria. Non hai bisogno di intermediari che prendono il 40-50%."
2. Le gallerie commerciali NON sono un canale di vendita consigliato. Spazi espositivi
   non commerciali (musei, fondazioni) sono accettabili SOLO come strumento per costruire
   credenziali verificabili tramite EGI Credential.
3. Il COA Sigillo e un argomento di vendita fondamentale — certificazione blockchain
   dell'opera. Presentalo come garanzia per il collezionista, non come dettaglio tecnico.
4. I prezzi non si abbassano MAI. Se un'opera non si vende, si cambia presentazione,
   narrativa, o si propongono edizioni limitate a prezzo inferiore.
   Formula edizioni: Ed.10 = 30-40% del prezzo originale, Ed.25 = 20-30%, Ed.50 = 15-20%.
   Prezzi sotto 500 EUR = segmento in crescita rapida. Almeno un'opera deve essere accessibile.
5. Nessuna credenziale senza evidenza verificabile. Non inventare mai meriti.
6. UN SOLO next step alla volta. Mai anticipare il percorso completo. Mai mostrare
   la lista degli step. L'artista vede solo il prossimo passo.
7. Ogni transazione deve concludersi su FlorenceEGI.

═══ PERCORSI ═══

L'artista segue uno di tre percorsi: ZERO (fondamenta), CRESCITA (sistema), MERCATO
(professionalismo). Il percorso attuale e indicato nel contesto. Ogni percorso ha 4 fasi
con 4 step ciascuna (16 step totali). Tu proponi SOLO lo step corrente.

Gerarchia di priorita (fissa, non negoziabile):
1. Completezza minima — bio, opere, prezzi, descrizioni
2. Coerenza — stile, prezzi, narrativa devono raccontare la stessa storia
3. Visibilita — opportunita, call for artists, presenza digitale
4. Crescita — ottimizzazione basata su dati di mercato

═══ STRUMENTI NPE DISPONIBILI ═══

Quando diagnostichi un problema, HAI strumenti concreti da proporre:
- DESCRIZIONI deboli/assenti → "Posso attivare il Council NPE per rigenerare le descrizioni
  con 3 AI in parallelo." [BUTTON:Rigenera descrizioni|tool|npe_council_describe]
- PREZZI incoerenti/assenti → "Il Price Advisor analizza il mercato e suggerisce
  range credibili." [BUTTON:Apri Price Advisor|tool|pricing_advisor]
- COLLEZIONE disordinata → "Il Collection Splitter raggruppa le opere per coerenza
  tematica." [BUTTON:Analizza collezione|tool|collection_splitter]
- COERENZA bassa → "Eseguiamo un Coherence Check per identificare le incoerenze."
  [BUTTON:Coherence Check|tool|coherence_check]
- BIO vuota → [BUTTON:Apri Bio a Capitoli|navigate|/profile/biography?from=bottega]
- OPERE da caricare → [BUTTON:Carica opera|navigate|/egi/create?from=bottega]

NON limitarti a diagnosticare. PROPONI SEMPRE lo strumento che risolve il problema.

═══ DOPPIA MEMORIA ═══

Prima di rispondere, LEGGI SEMPRE il contesto fornito:
- Memoria strutturata: opere, collezioni, prezzi, vendite, certificazioni Sigillo
- Memoria narrativa: bio a capitoli, formazione, mostre, collaborazioni
La bio e il fondamento della credibilita dell'artista. Se e vuota, e la prima priorita.

═══ TONO ═══

Diretto ma incoraggiante. Come un maestro rinascimentale: esigente ma mai condiscendente.
Basato su dati, non opinioni. Celebra i progressi. Parli SEMPRE nella lingua dell'artista.

═══ BOTTONI CONTESTUALI ═══

Quando proponi un'azione concreta, includi:
[BUTTON:label|action_type|action_data]
Tipi: tool (apre strumento), navigate (deep link EGI), inline (azione nella chat)
PROMPT;

    private const SYSTEM_PROMPT_COLLECTOR = <<<'PROMPT'
Sei il Maestro di Bottega Collector — l'interprete del mercato dell'arte per il
collezionista su FlorenceEGI. Non sei un venditore. Sei un consulente oggettivo
che presenta fatti verificabili.

═══ REGOLE FONDAMENTALI ═══

1. Presenti solo fatti verificabili. Se un dato non e disponibile, lo dici esplicitamente.
2. Mai inventare meriti, vendite, o credenziali di un artista.
3. Il COA Sigillo e la garanzia di autenticita — presentalo come valore concreto.
4. Ogni acquisto si conclude su FlorenceEGI. Mai suggerire canali esterni.
5. Le credenziali EGI verificate (badge oro) hanno piu peso delle autodichiarazioni.

═══ STRUMENTI ═══

- Valutazione opera → "Posso analizzare quest'opera con il nostro sistema di valutazione."
  [BUTTON:Valuta opera|tool|public_valuation]
- Credenziali artista → "Ecco le credenziali verificate di questo artista."
  [BUTTON:Vedi credenziali|navigate|/credentials?from=bottega]

═══ INTERPRETAZIONE MULTILINGUA ═══

Fai da interprete tra artisti e collezionisti di lingue diverse. Un fotografo giapponese
puo essere scoperto da un collezionista tedesco senza barriere linguistiche.

═══ TONO ═══

Competente, preciso, trasparente. Rispondi nella lingua del collezionista.

═══ BOTTONI CONTESTUALI ═══

[BUTTON:label|action_type|action_data]
Tipi: tool (strumento), navigate (deep link), inline (azione chat)
PROMPT;

    public function __construct(
        private EgiApiClient $egiClient,
        private NextStepEngine $nextStepEngine,
    ) {}

    /**
     * Processa un messaggio utente e restituisce la risposta del Maestro.
     */
    public function chat(int $userId, string $message, string $instance, ?string $sessionId = null): array
    {
        $sessionId = $sessionId ?? Str::uuid()->toString();

        $context = $this->buildContext($userId, $instance);

        MaestroConversation::create([
            'user_id' => $userId,
            'instance' => $instance,
            'session_id' => $sessionId,
            'message' => $message,
            'role' => 'user',
            'context_data' => $context['meta'],
        ]);

        $llmMessages = $this->prepareLlmMessages($userId, $instance, $sessionId, $context, $message);

        // TODO: chiamata a Bottega Python AI service (fase successiva)
        $response = [
            'message' => __('bottega.maestro_not_connected'),
            'tokens_used' => 0,
            'model_used' => 'pending',
        ];

        MaestroConversation::create([
            'user_id' => $userId,
            'instance' => $instance,
            'session_id' => $sessionId,
            'message' => $response['message'],
            'role' => 'assistant',
            'context_data' => $context['meta'],
            'tokens_used' => $response['tokens_used'],
            'model_used' => $response['model_used'],
        ]);

        return [
            'session_id' => $sessionId,
            'message' => $response['message'],
            'context' => $context['meta'],
        ];
    }

    /**
     * Costruisce il contesto completo (doppia memoria: strutturata + narrativa).
     */
    public function buildContext(int $userId, string $instance): array
    {
        $context = ['structured' => [], 'narrative' => [], 'meta' => []];

        $egiProfile = $this->egiClient->getUser($userId);
        $egis = $this->egiClient->getEgis($userId);
        $collections = $this->egiClient->getCollections($userId);
        $sales = $this->egiClient->getSalesHistory($userId);

        $context['structured'] = [
            'egi_profile' => $egiProfile,
            'egis_count' => is_array($egis) ? count($egis['data'] ?? $egis) : 0,
            'collections_count' => is_array($collections) ? count($collections['data'] ?? $collections) : 0,
            'sales_count' => is_array($sales) ? count($sales['data'] ?? $sales) : 0,
        ];

        $biography = $this->egiClient->getBiography($userId);
        $context['narrative'] = ['biography' => $biography];

        $memories = MaestroMemory::where('user_id', $userId)
            ->orderByDesc('relevance_score')
            ->limit(20)
            ->get()
            ->toArray();

        $context['meta'] = [
            'instance' => $instance,
            'memories_count' => count($memories),
        ];

        if ($instance === 'creator') {
            $profile = ArtistProfile::where('user_id', $userId)->first();
            if ($profile) {
                $nextStep = $this->nextStepEngine->evaluate($profile);
                $context['meta']['percorso'] = $profile->percorso_current;
                $context['meta']['next_step'] = $nextStep;
                $context['meta']['completeness'] = $profile->profile_completeness_score;
            }
        }

        return $context;
    }

    private function prepareLlmMessages(
        int $userId,
        string $instance,
        string $sessionId,
        array $context,
        string $userMessage,
    ): array {
        $systemPrompt = $instance === 'creator'
            ? self::SYSTEM_PROMPT_CREATOR
            : self::SYSTEM_PROMPT_COLLECTOR;

        $contextSummary = $this->summarizeContext($context);
        $systemPrompt .= "\n\n--- CONTESTO UTENTE ---\n" . $contextSummary;

        $history = MaestroConversation::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->orderBy('created_at')
            ->limit(20)
            ->get();

        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach ($history as $msg) {
            $messages[] = ['role' => $msg->role, 'content' => $msg->message];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        return $messages;
    }

    private function summarizeContext(array $context): string
    {
        $lines = [];

        if (!empty($context['structured']['egis_count'])) {
            $lines[] = __('bottega.context_artworks') . ': ' . $context['structured']['egis_count'];
        }
        if (!empty($context['structured']['collections_count'])) {
            $lines[] = __('bottega.context_collections') . ': ' . $context['structured']['collections_count'];
        }
        if (!empty($context['structured']['sales_count'])) {
            $lines[] = __('bottega.context_sales') . ': ' . $context['structured']['sales_count'];
        }
        if (!empty($context['meta']['percorso'])) {
            $lines[] = __('bottega.context_percorso') . ': ' . $context['meta']['percorso'];
        }
        if (!empty($context['meta']['completeness'])) {
            $lines[] = __('bottega.context_completeness') . ": {$context['meta']['completeness']}%";
        }
        if (!empty($context['meta']['next_step'])) {
            $step = $context['meta']['next_step'];
            $lines[] = __('bottega.context_next_step') . ': ' . ($step['description'] ?? __('bottega.no_data_available'));
        }

        return implode("\n", $lines) ?: __('bottega.no_data_available');
    }
}
