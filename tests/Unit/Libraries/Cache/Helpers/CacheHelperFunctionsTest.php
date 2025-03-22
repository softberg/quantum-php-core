<?php

namespace Quantum\Tests\Unit\Libraries\Cache\Helpers;

use Quantum\Libraries\Cache\Adapters\MemcachedAdapter;
use Quantum\Libraries\Cache\Adapters\DatabaseAdapter;
use Quantum\Libraries\Cache\Adapters\FileAdapter;
use Quantum\Libraries\Cache\Adapters\RedisAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Libraries\Cache\Cache;

class CacheHelperFunctionsTest extends AppTestCase
{

    public function testCacheHelperGetDefaultCache()
    {
        $this->assertInstanceOf(Cache::class, cache());

        $this->assertInstanceOf(FileAdapter::class, cache()->getAdapter());
    }

    public function testCacheHelperGetFileCache()
    {
        $this->assertInstanceOf(Cache::class, cache(Cache::FILE));

        $this->assertInstanceOf(FileAdapter::class, cache(Cache::FILE)->getAdapter());
    }

    public function testCacheHelperGetDatabaseCache()
    {
        $this->assertInstanceOf(Cache::class, cache(Cache::DATABASE));

        $this->assertInstanceOf(DatabaseAdapter::class, cache(Cache::DATABASE)->getAdapter());
    }

    public function testCacheHelperGetMemcachedCache()
    {
        $this->assertInstanceOf(Cache::class, cache(Cache::MEMCACHED));

        $this->assertInstanceOf(MemcachedAdapter::class, cache(Cache::MEMCACHED)->getAdapter());
    }

    public function testCacheHelperGetRedisCache()
    {
        $this->assertInstanceOf(Cache::class, cache(Cache::REDIS));

        $this->assertInstanceOf(RedisAdapter::class, cache(Cache::REDIS)->getAdapter());
    }
}