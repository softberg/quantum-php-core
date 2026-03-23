<?php

namespace Quantum\Tests\Unit\Cache\Helpers;

use Quantum\Cache\Adapters\MemcachedAdapter;
use Quantum\Cache\Adapters\DatabaseAdapter;
use Quantum\Cache\Adapters\FileAdapter;
use Quantum\Cache\Adapters\RedisAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Cache\Enums\CacheType;
use Quantum\Cache\Cache;

class CacheHelperFunctionsTest extends AppTestCase
{
    public function testCacheHelperGetDefaultCache(): void
    {
        $this->assertInstanceOf(Cache::class, cache());

        $this->assertInstanceOf(FileAdapter::class, cache()->getAdapter());
    }

    public function testCacheHelperGetFileCache(): void
    {
        $this->assertInstanceOf(Cache::class, cache(CacheType::FILE));

        $this->assertInstanceOf(FileAdapter::class, cache(CacheType::FILE)->getAdapter());
    }

    public function testCacheHelperGetDatabaseCache(): void
    {
        $this->assertInstanceOf(Cache::class, cache(CacheType::DATABASE));

        $this->assertInstanceOf(DatabaseAdapter::class, cache(CacheType::DATABASE)->getAdapter());
    }

    public function testCacheHelperGetMemcachedCache(): void
    {
        $this->assertInstanceOf(Cache::class, cache(CacheType::MEMCACHED));

        $this->assertInstanceOf(MemcachedAdapter::class, cache(CacheType::MEMCACHED)->getAdapter());
    }

    public function testCacheHelperGetRedisCache(): void
    {
        $this->assertInstanceOf(Cache::class, cache(CacheType::REDIS));

        $this->assertInstanceOf(RedisAdapter::class, cache(CacheType::REDIS)->getAdapter());
    }
}
