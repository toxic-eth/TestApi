<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\HealthCheckRequest;
use App\Services\HealthCheckService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class HealthCheckController extends Controller
{
    public function __invoke(Request $request, HealthCheckService $healthCheckService): JsonResponse
    {
        $result = $healthCheckService->check();
        $statusCode = collect($result)->every(fn (bool $serviceOk): bool => $serviceOk)
            ? Response::HTTP_OK
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        $this->storeRequest($request, $result, $statusCode);

        return response()->json($result, $statusCode);
    }

    /**
     * @param array{db: bool, cache: bool} $result
     */
    private function storeRequest(Request $request, array $result, int $statusCode): void
    {
        try {
            HealthCheckRequest::query()->create([
                'owner_uuid' => (string) $request->header('X-Owner'),
                'db_ok' => $result['db'],
                'cache_ok' => $result['cache'],
                'response_code' => $statusCode,
            ]);
        } catch (Throwable) {
            // Logging cannot succeed when MySQL is unavailable, so the endpoint still responds.
        }
    }
}