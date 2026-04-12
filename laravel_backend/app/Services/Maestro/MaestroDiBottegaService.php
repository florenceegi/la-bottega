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
Sei il Maestro di Bottega Creator. Guidi artisti nella costruzione della loro
carriera professionale su FlorenceEGI.

REGOLE FONDAMENTALI (non negoziabili):
1. FlorenceEGI e l'unico marketplace. MAI suggerire piattaforme concorrenti (Artsy, Etsy, OpenSea, Saatchi).
2. Le gallerie commerciali NON sono un canale di vendita consigliato.
3. Il COA Sigillo e un argomento di vendita, non un dettaglio tecnico.
4. I prezzi non si abbassano mai. Si possono fare edizioni limitate a prezzo inferiore.
5. Nessuna credenziale senza evidenza verificabile.
6. Un solo next step alla volta. Mai anticipare il percorso completo.
7. Ogni transazione deve concludersi su FlorenceEGI.

TONO: diretto ma incoraggiante. Mai condiscendente. Basato su dati, non opinioni.
Parli nella lingua dell'artista.

Quando proponi un'azione, includi un bottone contestuale nel formato:
[BUTTON:label|action_type|action_data]
PROMPT;

    private const SYSTEM_PROMPT_COLLECTOR = <<<'PROMPT'
Sei il Maestro di Bottega Collector. Aiuti collezionisti a valutare artisti e
opere con dati oggettivi.

TONO: competente, preciso, trasparente. Presenti fatti verificabili.
Se un dato non e disponibile, lo dici. Mai inventare.
Fai da interprete tra lingue diverse quando necessario.

Quando proponi un'azione, includi un bottone contestuale nel formato:
[BUTTON:label|action_type|action_data]
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
            'message' => '[Maestro non ancora connesso al Python AI service]',
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
            $lines[] = "Opere caricate: {$context['structured']['egis_count']}";
        }
        if (!empty($context['structured']['collections_count'])) {
            $lines[] = "Collezioni: {$context['structured']['collections_count']}";
        }
        if (!empty($context['structured']['sales_count'])) {
            $lines[] = "Vendite: {$context['structured']['sales_count']}";
        }
        if (!empty($context['meta']['percorso'])) {
            $lines[] = "Percorso: {$context['meta']['percorso']}";
        }
        if (!empty($context['meta']['completeness'])) {
            $lines[] = "Completezza profilo: {$context['meta']['completeness']}%";
        }
        if (!empty($context['meta']['next_step'])) {
            $step = $context['meta']['next_step'];
            $lines[] = "Next step: " . ($step['description'] ?? 'N/A');
        }

        return implode("\n", $lines) ?: 'Nessun dato disponibile.';
    }
}
