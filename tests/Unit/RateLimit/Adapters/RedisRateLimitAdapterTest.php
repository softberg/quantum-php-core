<?php

namespace Quantum\Tests\Unit\RateLimit\Adapters;

use Quantum\RateLimit\Adapters\RedisRateLimitAdapter;
use Quantum\Tests\Helpers\InMemoryPsrCache;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Cache\Cache;

class RedisRateLimitAdapterTest extends AppTestCase
{
    public function testRedisAdapterHitAndResetFlow(): void
    {
        $cache = new Cache(new InMemoryPsrCache());
        $adapter = new RedisRateLimitAdapter($cache, 30);

        $this->assertTrue($adapter->hit('k2', 1, 60));
        $this->assertFalse($adapter->hit('k2', 1, 60));

        $adapter->reset('k2', 1);
        $this->assertFalse($adapter->hit('k2', 1, 60));
    }
}
