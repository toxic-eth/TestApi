<?php

namespace App\Services;

use App\Data\HealthCheckResult;
use App\Models\HealthCheckRequest;
use Illuminate\Support\Facades\Log;
use Throwable;

class HealthCheckRequestLogger
{
    public function log(string $ownerUuid, HealthCheckResult $result, int $responseCode): void
    {
        try {
            HealthCheckRequest::query()->create([
                'owner_uuid' => $ownerUuid,
                'db_ok' => $result->db,
                'cache_ok' => $result->cache,
                'response_code' => $responseCode,
            ]);
        } catch (Throwable $exception) {
            Log::warning('Failed to persist health-check request.', [
                'owner_uuid' => $ownerUuid,
                'response_code' => $responseCode,
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
