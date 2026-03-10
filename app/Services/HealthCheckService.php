<?php

namespace App\Services;

use App\Data\HealthCheckResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Throwable;

class HealthCheckService
{
    public function check(): HealthCheckResult
    {
        return new HealthCheckResult(
            db: $this->checkDatabase(),
            cache: $this->checkCache(),
        );
    }

    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            DB::select('SELECT 1');

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    private function checkCache(): bool
    {
        $key = 'health-check:'.bin2hex(random_bytes(8));
        $value = 'ok';

        try {
            $redis = Redis::connection('cache');
            $redis->setex($key, 5, $value);

            return $redis->get($key) === $value;
        } catch (Throwable) {
            return false;
        }
    }
}
