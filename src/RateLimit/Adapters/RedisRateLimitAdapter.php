<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\RateLimit\Adapters;

use Quantum\RateLimit\Contracts\RateLimitAdapterInterface;
use RedisException;
use Redis;

class RedisRateLimitAdapter implements RateLimitAdapterInterface
{
    private Redis $redis;

    private int $resetInterval;

    /**
     * @param array<string, mixed> $params
     * @throws RedisException
     */
    public function __construct(array $params)
    {
        $this->resetInterval = $params['ttl'];

        $this->redis = new Redis();
        $this->redis->connect($params['host'], $params['port']);
    }

    public function hit(string $key, int $limit, int $interval): bool
    {
        $count = (int) $this->redis->incr($key);

        if ($count === 1) {
            $this->redis->expire($key, $interval);
        }

        return $count <= $limit;
    }

    public function reset(string $key, int $count = 0): void
    {
        if ($count <= 0) {
            $this->redis->del($key);
            return;
        }

        $this->redis->setex($key, $this->resetInterval, (string) $count);
    }

    public function retryAfter(string $key): int
    {
        $ttl = (int) $this->redis->ttl($key);
        return $ttl > 0 ? $ttl : 0;
    }
}
