<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Throwable;

class HealthCheckService
{
    /**
     * @return array{db: bool, cache: bool}
     */
    public function check(): array
    {
        return [
            'db' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
        ];
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