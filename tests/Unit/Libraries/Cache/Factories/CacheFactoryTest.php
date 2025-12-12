<?php

namespace Quantum\Tests\Unit\Libraries\Cache\Factories;

use Quantum\Libraries\Cache\Exceptions\CacheException;
use Quantum\Libraries\Cache\Adapters\MemcachedAdapter;
use Quantum\Libraries\Cache\Adapters\DatabaseAdapter;
use Quantum\Libraries\Cache\Factories\CacheFactory;
use Quantum\Libraries\Cache\Adapters\RedisAdapter;
use Quantum\Libraries\Cache\Adapters\FileAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Libraries\Cache\Cache;

class CacheFactoryTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->setPrivateProperty(CacheFactory::class, 'instances', []);
    }

    public function testCacheFactoryInstance()
    {
        $cache = CacheFactory::get();

        $this->assertInstanceOf(Cache::class, $cache);
    }

    public function testCacheFactoryDefaultAdapter()
    {
        $cache = CacheFactory::get();

        $this->assertInstanceOf(FileAdapter::class, $cache->getAdapter());
    }

    public function testCacheFactoryFileAdapter()
    {
        $cache = CacheFactory::get(Cache::FILE);

        $this->assertInstanceOf(FileAdapter::class, $cache->getAdapter());
    }

    public function testCacheFactoryDatabaseAdapter()
    {
        $cache = CacheFactory::get(Cache::DATABASE);

        $this->assertInstanceOf(DatabaseAdapter::class, $cache->getAdapter());
    }

    public function testCacheFactoryMemcachedAdapter()
    {
        $cache = CacheFactory::get(Cache::MEMCACHED);

        $this->assertInstanceOf(MemcachedAdapter::class, $cache->getAdapter());
    }

    public function testCacheFactoryRedisAdapter()
    {
        $cache = CacheFactory::get(Cache::REDIS);

        $this->assertInstanceOf(RedisAdapter::class, $cache->getAdapter());
    }

    public function testCacheFactoryInvalidTypeAdapter()
    {
        $this->expectException(CacheException::class);

        $this->expectExceptionMessage('The adapter `invalid_type` is not supported');

        CacheFactory::get('invalid_type');
    }

    public function testCacheFactoryReturnsSameInstance()
    {
        $cache1 = CacheFactory::get(Cache::FILE);
        $cache2 = CacheFactory::get(Cache::FILE);

        $this->assertSame($cache1, $cache2);
    }
}