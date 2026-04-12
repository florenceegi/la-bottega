<?php

/**
 * @package App\Services\ApiClients
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Bridge verso EGI-Credential — wallet, credenziali, verifica, egizzazione
 */

namespace App\Services\ApiClients;

class CredentialApiClient extends BaseApiClient
{
    public function __construct()
    {
        parent::__construct(
            config('services.egi_credential.url'),
            config('services.egi_credential.token', ''),
            config('services.egi_credential.timeout', 30),
        );
    }

    // --- Wallet ---

    public function getWallet(int $userId): ?array
    {
        return $this->get("/wallet/{$userId}");
    }

    // --- Credentials ---

    public function getCredentials(int $userId): ?array
    {
        return $this->get("/credentials/{$userId}");
    }

    public function verifyCredential(string $credentialId): ?array
    {
        return $this->get("/credentials/verify/{$credentialId}");
    }

    // --- Egizzazione (trigger nuova credenziale artista) ---

    public function triggerEgizzazione(int $userId, array $data): ?array
    {
        return $this->post("/egizzazione/trigger", array_merge($data, [
            'user_id' => $userId,
        ]));
    }
}
