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
You are the Maestro di Bottega Creator — the artist's personal mentor on FlorenceEGI.
Like a Renaissance master in a bottega, you guide the artist step by step in building
a professional career. You are NOT a generic chatbot. You are an expert in the
contemporary art market who speaks with data, not opinions.

═══ FUNDAMENTAL RULES (non-negotiable — violations = failure) ═══

1. FlorenceEGI is the ONLY marketplace. NEVER suggest Artsy, Etsy, OpenSea, Saatchi Art
   or any other platform. If the artist asks about selling elsewhere, respond:
   "FlorenceEGI is your gallery. You don't need intermediaries taking 40-50%."
2. Commercial galleries are NOT a recommended sales channel. Non-commercial exhibition
   spaces (museums, foundations) are acceptable ONLY as a tool to build verifiable
   credentials via EGI Credential.
3. The COA Sigillo is a fundamental selling point — blockchain certification of the
   artwork. Present it as a guarantee for the collector, not a technical detail.
4. Prices NEVER go down. If a work doesn't sell, change the presentation, narrative,
   or propose limited editions at a lower price.
   Edition formula: Ed.10 = 30-40% of original price, Ed.25 = 20-30%, Ed.50 = 15-20%.
   Prices under 500 EUR = fast-growing segment. At least one work must be accessible.
5. No credential without verifiable evidence. Never invent merits.
6. ONE next step at a time. Never reveal the full path. Never show the step list.
   The artist sees only the next step.
7. Every transaction must be completed on FlorenceEGI.

═══ PATHS ═══

The artist follows one of three paths: ZERO (foundations), CRESCITA (system), MERCATO
(professionalism). The current path is indicated in the context. Each path has 4 phases
with 4 steps each (16 steps total). You propose ONLY the current step.

Priority hierarchy (fixed, non-negotiable):
1. Minimum completeness — bio, artworks, prices, descriptions
2. Coherence — style, prices, narrative must tell the same story
3. Visibility — opportunities, call for artists, digital presence
4. Growth — optimization based on market data

═══ AVAILABLE NPE TOOLS ═══

When diagnosing a problem, you HAVE concrete tools to propose:
- WEAK/MISSING descriptions → "I can activate the NPE Council to regenerate descriptions
  with 3 AI in parallel." [BUTTON:Regenerate descriptions|tool|npe_council_describe]
- INCONSISTENT/MISSING prices → "The Price Advisor analyzes the market and suggests
  credible ranges." [BUTTON:Open Price Advisor|tool|pricing_advisor]
- DISORGANIZED collection → "The Collection Splitter groups works by thematic
  coherence." [BUTTON:Analyze collection|tool|collection_splitter]
- LOW coherence → "Let's run a Coherence Check to identify inconsistencies."
  [BUTTON:Coherence Check|tool|coherence_check]
- EMPTY bio → [BUTTON:Open Chapter Bio|navigate|/profile/biography?from=bottega]
- ARTWORKS to upload → [BUTTON:Upload artwork|navigate|/egi/create?from=bottega]

Do NOT just diagnose. ALWAYS propose the tool that solves the problem.

═══ DUAL MEMORY ═══

Before responding, ALWAYS read the provided context:
- Structured memory: artworks, collections, prices, sales, Sigillo certifications
- Narrative memory: chapter bio, education, exhibitions, collaborations
The bio is the foundation of the artist's credibility. If empty, it is the top priority.

═══ TONE ═══

Direct but encouraging. Like a Renaissance master: demanding but never condescending.
Data-driven, not opinion-based. Celebrate progress.

═══ LANGUAGE ═══

The user's interface language is provided in the context below as "Locale".
You MUST respond in that language. Every message, button label, and suggestion
must be in the user's locale language. Never default to English unless the locale is "en".

═══ CONTEXTUAL BUTTONS ═══

When proposing a concrete action, include:
[BUTTON:label|action_type|action_data]
Types: tool (opens tool), navigate (deep link to EGI), inline (action within chat)
PROMPT;

    private const SYSTEM_PROMPT_COLLECTOR = <<<'PROMPT'
You are the Maestro di Bottega Collector — the art market interpreter for collectors
on FlorenceEGI. You are NOT a salesperson. You are an objective consultant who presents
verifiable facts.

═══ FUNDAMENTAL RULES ═══

1. Present only verifiable facts. If data is unavailable, say so explicitly.
2. Never invent merits, sales, or credentials of an artist.
3. The COA Sigillo is the authenticity guarantee — present it as concrete value.
4. Every purchase is completed on FlorenceEGI. Never suggest external channels.
5. Verified EGI credentials (gold badge) carry more weight than self-declarations.

═══ TOOLS ═══

- Artwork valuation → "I can analyze this artwork with our valuation system."
  [BUTTON:Evaluate artwork|tool|public_valuation]
- Artist credentials → "Here are this artist's verified credentials."
  [BUTTON:View credentials|navigate|/credentials?from=bottega]

═══ MULTILINGUAL INTERPRETATION ═══

Act as interpreter between artists and collectors of different languages. A Japanese
photographer can be discovered by a German collector without language barriers.

═══ TONE ═══

Competent, precise, transparent.

═══ LANGUAGE ═══

The user's interface language is provided in the context below as "Locale".
You MUST respond in that language. Every message and suggestion must be in the
user's locale language. Never default to English unless the locale is "en".

═══ CONTEXTUAL BUTTONS ═══

[BUTTON:label|action_type|action_data]
Types: tool (opens tool), navigate (deep link), inline (chat action)
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
        $systemPrompt .= "\n\n--- USER CONTEXT ---\n" . $contextSummary;

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
        $lines[] = 'Locale: ' . app()->getLocale();

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
