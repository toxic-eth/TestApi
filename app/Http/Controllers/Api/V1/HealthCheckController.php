<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\HealthCheckRequestLogger;
use App\Services\HealthCheckService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HealthCheckController extends Controller
{
    public function __invoke(
        Request $request,
        HealthCheckService $healthCheckService,
        HealthCheckRequestLogger $healthCheckRequestLogger,
    ): JsonResponse {
        $result = $healthCheckService->check();
        $statusCode = $result->isHealthy()
            ? Response::HTTP_OK
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        $healthCheckRequestLogger->log(
            ownerUuid: (string) $request->header('X-Owner'),
            result: $result,
            responseCode: $statusCode,
        );

        return response()->json($result->toArray(), $statusCode);
    }
}
