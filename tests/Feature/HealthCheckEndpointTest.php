<?php

namespace Tests\Feature;

use App\Data\HealthCheckResult;
use App\Services\HealthCheckService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class HealthCheckEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_ok_when_all_services_are_available(): void
    {
        $this->mockHealthCheckService(new HealthCheckResult(db: true, cache: true));

        $response = $this->getJson('/api/v1/health-check', [
            'X-Owner' => $this->validOwner(),
        ]);

        $response
            ->assertOk()
            ->assertExactJson([
                'db' => true,
                'cache' => true,
            ]);
    }

    public function test_it_returns_internal_server_error_when_any_service_is_unavailable(): void
    {
        $this->mockHealthCheckService(new HealthCheckResult(db: true, cache: false));

        $response = $this->getJson('/api/v1/health-check', [
            'X-Owner' => $this->validOwner(),
        ]);

        $response
            ->assertStatus(500)
            ->assertExactJson([
                'db' => true,
                'cache' => false,
            ]);
    }

    public function test_it_requires_the_x_owner_header(): void
    {
        $response = $this->getJson('/api/v1/health-check');

        $response
            ->assertUnprocessable()
            ->assertExactJson([
                'message' => 'The X-Owner header must contain a valid UUID.',
            ]);
    }

    public function test_it_requires_a_valid_uuid_in_the_x_owner_header(): void
    {
        $response = $this->getJson('/api/v1/health-check', [
            'X-Owner' => 'not-a-uuid',
        ]);

        $response
            ->assertUnprocessable()
            ->assertExactJson([
                'message' => 'The X-Owner header must contain a valid UUID.',
            ]);
    }

    public function test_it_logs_each_health_check_request(): void
    {
        $owner = $this->validOwner();
        $this->mockHealthCheckService(new HealthCheckResult(db: true, cache: true));

        $this->getJson('/api/v1/health-check', [
            'X-Owner' => $owner,
        ])->assertOk();

        $this->assertDatabaseHas('health_check_requests', [
            'owner_uuid' => $owner,
            'db_ok' => true,
            'cache_ok' => true,
            'response_code' => 200,
        ]);
    }

    public function test_it_throttles_requests_after_sixty_requests_per_minute(): void
    {
        $this->mockHealthCheckService(new HealthCheckResult(db: true, cache: true));
        $headers = ['X-Owner' => $this->validOwner()];

        for ($index = 0; $index < 60; $index++) {
            $this->getJson('/api/v1/health-check', $headers)->assertOk();
        }

        $this->getJson('/api/v1/health-check', $headers)->assertStatus(429);
    }

    private function mockHealthCheckService(HealthCheckResult $result): void
    {
        $mock = Mockery::mock(HealthCheckService::class);
        $mock->shouldReceive('check')->andReturn($result);

        $this->app->instance(HealthCheckService::class, $mock);
    }

    private function validOwner(): string
    {
        return Str::uuid()->toString();
    }
}
