<?php

namespace App\Data;

final readonly class HealthCheckResult
{
    public function __construct(
        public bool $db,
        public bool $cache,
    ) {
    }

    public function isHealthy(): bool
    {
        return $this->db && $this->cache;
    }

    /**
     * @return array{db: bool, cache: bool}
     */
    public function toArray(): array
    {
        return [
            'db' => $this->db,
            'cache' => $this->cache,
        ];
    }
}
