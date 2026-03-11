<?php

namespace Quantum\Tests\Unit\Cache\Factories;

use Quantum\Cache\Exceptions\CacheException;
use Quantum\Cache\Adapters\MemcachedAdapter;
use Quantum\Cache\Adapters\DatabaseAdapter;
use Quantum\Cache\Factories\CacheFactory;
use Quantum\Cache\Adapters\RedisAdapter;
use Quantum\Cache\Adapters\FileAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Cache\Cache;

class CacheFactoryTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setPrivateProperty(CacheFactory::class, 'instances', []);
    }

    public function testCacheFactoryInstance(): void
    {
        $cache = CacheFactory::get();

        $this->assertInstanceOf(Cache::class, $cache);
    }

    public function testCacheFactoryDefaultAdapter(): void
    {
        $cache = CacheFactory::get();

        $this->assertInstanceOf(FileAdapter::class, $cache->getAdapter());
    }

    public function testCacheFactoryFileAdapter(): void
    {
        $cache = CacheFactory::get(Cache::FILE);

        $this->assertInstanceOf(FileAdapter::class, $cache->getAdapter());
    }

    public function testCacheFactoryDatabaseAdapter(): void
    {
        $cache = CacheFactory::get(Cache::DATABASE);

        $this->assertInstanceOf(DatabaseAdapter::class, $cache->getAdapter());
    }

    public function testCacheFactoryMemcachedAdapter(): void
    {
        $cache = CacheFactory::get(Cache::MEMCACHED);

        $this->assertInstanceOf(MemcachedAdapter::class, $cache->getAdapter());
    }

    public function testCacheFactoryRedisAdapter(): void
    {
        $cache = CacheFactory::get(Cache::REDIS);

        $this->assertInstanceOf(RedisAdapter::class, $cache->getAdapter());
    }

    public function testCacheFactoryInvalidTypeAdapter(): void
    {
        $this->expectException(CacheException::class);

        $this->expectExceptionMessage('The adapter `invalid_type` is not supported');

        CacheFactory::get('invalid_type');
    }

    public function testCacheFactoryReturnsSameInstance(): void
    {
        $cache1 = CacheFactory::get(Cache::FILE);
        $cache2 = CacheFactory::get(Cache::FILE);

        $this->assertSame($cache1, $cache2);
    }
}
