<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Health check enterprise — readiness deep check per ALB + CI/CD
 */
class HealthController extends Controller
{
    protected ErrorManagerInterface $errorManager;

    public function __construct(ErrorManagerInterface $errorManager)
    {
        $this->errorManager = $errorManager;
    }

    public function __invoke(): JsonResponse
    {
        $checks  = [];
        $healthy = true;

        // --- Database (PostgreSQL RDS) ---
        try {
            DB::select('SELECT 1');
            $checks['database'] = ['status' => 'ok'];
        } catch (\Throwable $e) {
            $checks['database'] = ['status' => 'error', 'message' => $e->getMessage()];
            $healthy = false;
        }

        // --- Redis (cache + sessions) ---
        if (config('database.redis.default.host') !== null) {
            try {
                Redis::ping();
                $checks['redis'] = ['status' => 'ok'];
            } catch (\Throwable $e) {
                $checks['redis'] = ['status' => 'error', 'message' => $e->getMessage()];
                $healthy = false;
            }
        }

        // --- Disk space ---
        $diskFree    = disk_free_space('/');
        $diskTotal   = disk_total_space('/');
        $diskUsedPct = $diskTotal > 0 ? round((1 - $diskFree / $diskTotal) * 100, 1) : 0;
        $checks['disk'] = [
            'status'   => $diskUsedPct < 90 ? 'ok' : 'warning',
            'used_pct' => $diskUsedPct,
        ];
        if ($diskUsedPct >= 95) {
            $healthy = false;
        }

        // --- Bottega Python FastAPI (Maestro AI) — warning only, non bloccante ---
        $pythonUrl = config('services.bottega_python.url', 'http://127.0.0.1:8002');
        try {
            $response   = Http::timeout(3)->get("{$pythonUrl}/health");
            $statusCode = $response->getStatusCode();
            $checks['bottega_python'] = $statusCode >= 200 && $statusCode < 300
                ? ['status' => 'ok']
                : ['status' => 'warning', 'code' => $statusCode];
        } catch (\Throwable $e) {
            $checks['bottega_python'] = ['status' => 'warning', 'message' => $e->getMessage()];
        }

        $status   = $healthy ? 'healthy' : 'unhealthy';
        $httpCode = $healthy ? 200 : 503;

        return response()->json([
            'status'  => $status,
            'organ'   => config('app.name'),
            'env'     => config('app.env'),
            'version' => config('app.version', 'unknown'),
            'checks'  => $checks,
            'ts'      => now()->toIso8601String(),
        ], $httpCode);
    }
}
