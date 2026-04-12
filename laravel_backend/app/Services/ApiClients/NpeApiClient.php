<?php

/**
 * @package App\Services\ApiClients
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Bridge verso NPE — Council, Pricing, Social, Collection coherence
 */

namespace App\Services\ApiClients;

class NpeApiClient extends BaseApiClient
{
    public function __construct()
    {
        parent::__construct(
            config('services.egi.url'),
            config('services.egi.token', ''),
            config('services.egi.timeout', 30),
            config('services.egi.retry', 2),
        );
    }

    // --- Council (descrizioni opere) ---

    public function generateDescription(int $egiId, string $language = 'it'): ?array
    {
        return $this->post("/internal/bottega/npe/council/describe", [
            'egi_id' => $egiId,
            'language' => $language,
        ]);
    }

    public function bulkDescriptions(array $egiIds, string $language = 'it'): ?array
    {
        return $this->post("/internal/bottega/npe/council/bulk-describe", [
            'egi_ids' => $egiIds,
            'language' => $language,
        ]);
    }

    // --- Pricing Advisor ---

    public function getPriceAdvisorResult(int $egiId): ?array
    {
        return $this->get("/internal/bottega/npe/pricing/{$egiId}");
    }

    // --- Social (campagne, publishing, analytics) ---

    public function getCampaigns(int $userId): ?array
    {
        return $this->get("/internal/bottega/npe/social/campaigns/{$userId}");
    }

    public function getPublishedPosts(int $userId, array $filters = []): ?array
    {
        return $this->get("/internal/bottega/npe/social/posts/{$userId}", $filters);
    }

    public function getSocialAnalytics(int $userId): ?array
    {
        return $this->get("/internal/bottega/npe/social/analytics/{$userId}");
    }

    // --- Collection coherence (CollectionSplitter) ---

    public function getCoherenceScore(int $collectionId): ?array
    {
        return $this->get("/internal/bottega/npe/collection/{$collectionId}/coherence");
    }

    public function splitCollection(int $collectionId): ?array
    {
        return $this->post("/internal/bottega/npe/collection/{$collectionId}/split");
    }
}
