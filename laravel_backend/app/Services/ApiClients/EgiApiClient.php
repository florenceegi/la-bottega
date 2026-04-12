<?php

/**
 * @package App\Services\ApiClients
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Client HTTP autenticato verso EGI core — profili, opere, collezioni, traits, bio, blockchain
 */

namespace App\Services\ApiClients;

class EgiApiClient extends BaseApiClient
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

    // --- User Profile ---

    public function getUser(int $userId): ?array
    {
        return $this->get("/internal/bottega/user/{$userId}/profile");
    }

    // --- Opere (Egis) ---

    public function getEgis(int $userId, array $filters = []): ?array
    {
        return $this->get("/internal/bottega/user/{$userId}/egis", $filters);
    }

    public function getEgi(int $egiId): ?array
    {
        return $this->get("/internal/bottega/egi/{$egiId}");
    }

    // --- Collections ---

    public function getCollections(int $userId): ?array
    {
        return $this->get("/internal/bottega/user/{$userId}/collections");
    }

    // --- Traits ---

    public function getTraits(int $egiId): ?array
    {
        return $this->get("/internal/bottega/egi/{$egiId}/traits");
    }

    public function getCoaTraits(int $egiId): ?array
    {
        return $this->get("/internal/bottega/egi/{$egiId}/coa-traits");
    }

    // --- Biography ---

    public function getBiography(int $userId): ?array
    {
        return $this->get("/internal/bottega/user/{$userId}/biography");
    }

    public function getBiographyChapters(int $userId): ?array
    {
        return $this->get("/internal/bottega/user/{$userId}/biography/chapters");
    }

    // --- Blockchain ---

    public function getBlockchainStatus(int $userId): ?array
    {
        return $this->get("/internal/bottega/user/{$userId}/blockchain");
    }

    // --- Sales ---

    public function getSalesHistory(int $userId, array $filters = []): ?array
    {
        return $this->get("/internal/bottega/user/{$userId}/sales", $filters);
    }
}
