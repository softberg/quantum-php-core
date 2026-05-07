<?php

namespace Quantum\Tests\Unit\RateLimit\Adapters;

use Quantum\RateLimit\Adapters\FileRateLimitAdapter;
use Quantum\Tests\Helpers\InMemoryPsrCache;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Cache\Cache;

class FileRateLimitAdapterTest extends AppTestCase
{
    public function testFileAdapterHitAndResetFlow(): void
    {
        $cache = new Cache(new InMemoryPsrCache());
        $adapter = new FileRateLimitAdapter($cache, 30);

        $this->assertTrue($adapter->hit('k1', 2, 60));
        $this->assertTrue($adapter->hit('k1', 2, 60));
        $this->assertFalse($adapter->hit('k1', 2, 60));

        $adapter->reset('k1');
        $this->assertTrue($adapter->hit('k1', 2, 60));
    }
}
