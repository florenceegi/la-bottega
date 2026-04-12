<?php

/**
 * @package App\Services\ApiClients
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Client HTTP base per comunicazione inter-organo con retry e timeout
 */

namespace App\Services\ApiClients;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseApiClient
{
    protected string $baseUrl;
    protected string $token;
    protected int $timeout;
    protected int $retry;

    public function __construct(string $baseUrl, string $token, int $timeout = 30, int $retry = 2)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = $token;
        $this->timeout = $timeout;
        $this->retry = $retry;
    }

    protected function request(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withToken($this->token)
            ->timeout($this->timeout)
            ->retry($this->retry, 500)
            ->acceptJson();
    }

    protected function get(string $path, array $query = []): ?array
    {
        return $this->send('get', $path, ['query' => $query]);
    }

    protected function post(string $path, array $data = []): ?array
    {
        return $this->send('post', $path, ['json' => $data]);
    }

    private function send(string $method, string $path, array $options): ?array
    {
        try {
            /** @var Response $response */
            $response = $method === 'get'
                ? $this->request()->get($path, $options['query'] ?? [])
                : $this->request()->post($path, $options['json'] ?? []);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Bottega API call failed', [
                'client' => static::class,
                'method' => $method,
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Bottega API call exception', [
                'client' => static::class,
                'method' => $method,
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
