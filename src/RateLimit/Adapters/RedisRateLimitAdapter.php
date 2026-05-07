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
use Quantum\Cache\Cache;

class RedisRateLimitAdapter implements RateLimitAdapterInterface
{
    private Cache $cache;

    private int $resetInterval;

    public function __construct(?Cache $cache = null, int $resetInterval = 60)
    {
        $this->cache = $cache ?? cache('redis');
        $this->resetInterval = $resetInterval;
    }

    public function hit(string $key, int $limit, int $interval): bool
    {
        $now = time();
        $data = $this->cache->get($key);

        if (!is_array($data) || !isset($data['count'], $data['reset_at']) || $now >= (int) $data['reset_at']) {
            $count = 0;
            $resetAt = $now + $interval;
        } else {
            $count = (int) $data['count'];
            $resetAt = (int) $data['reset_at'];
        }

        $count++;

        $ttl = max(1, $resetAt - $now);

        $this->cache->set($key, [
            'count' => $count,
            'reset_at' => $resetAt,
        ], $ttl);

        return $count <= $limit;
    }

    public function reset(string $key, int $count = 0): void
    {
        if ($count <= 0) {
            $this->cache->delete($key);
            return;
        }

        $this->cache->set($key, [
            'count' => $count,
            'reset_at' => time() + $this->resetInterval,
        ], $this->resetInterval);
    }

    public function retryAfter(string $key): int
    {
        $data = $this->cache->get($key);

        if (!is_array($data) || !isset($data['reset_at'])) {
            return 0;
        }

        return max(0, (int) $data['reset_at'] - time());
    }
}
