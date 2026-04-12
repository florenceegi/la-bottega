<?php

/**
 * @package App\Services\ApiClients
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Bridge verso EgiliService EGI — balance, spend, earn, availability check
 */

namespace App\Services\ApiClients;

class EgiliApiClient extends BaseApiClient
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

    // --- Balance ---

    public function getBalance(int $userId): ?array
    {
        return $this->get("/internal/bottega/egili/balance/{$userId}");
    }

    /**
     * Verifica se l'utente ha abbastanza Egili per uno strumento.
     * Costi da core.ai_feature_pricing — MAI hardcoded (feedback Fabio).
     */
    public function checkAvailability(int $userId, string $featureKey): ?array
    {
        return $this->get("/internal/bottega/egili/check", [
            'user_id' => $userId,
            'feature_key' => $featureKey,
        ]);
    }

    // --- Spend ---

    public function spend(int $userId, string $featureKey, array $metadata = []): ?array
    {
        return $this->post("/internal/bottega/egili/spend", [
            'user_id' => $userId,
            'feature_key' => $featureKey,
            'metadata' => $metadata,
        ]);
    }

    // --- Earn ---

    public function earn(int $userId, int $amount, string $reason, array $metadata = []): ?array
    {
        return $this->post("/internal/bottega/egili/earn", [
            'user_id' => $userId,
            'amount' => $amount,
            'reason' => $reason,
            'metadata' => $metadata,
        ]);
    }
}
