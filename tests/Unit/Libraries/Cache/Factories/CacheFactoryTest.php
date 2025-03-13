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

        $this->setPrivateProperty(CacheFactory::class, 'instance', null);
    }

    public function testCacheFactoryInstance()
    {
        $cache = CacheFactory::get();

        $this->assertInstanceOf(Cache::class, $cache);
    }

    public function testCacheFactoryFileAdapter()
    {
        $cache = CacheFactory::get();

        $this->assertInstanceOf(FileAdapter::class, $cache->getAdapter());
    }

    public function testCacheFactoryDatabaseAdapter()
    {
        $params = [
            'prefix' => 'test',
            'table' => 'cache',
            'ttl' => 60
        ];

        config()->set('cache.default', 'database');

        config()->set('cache.database', $params);

        $cache = CacheFactory::get();

        $this->assertInstanceOf(DatabaseAdapter::class, $cache->getAdapter());
    }

    public function testCacheFactoryMemcachedAdapter()
    {
        $params = [
            'prefix' => 'test',
            'host' => '127.0.0.1',
            'port' => 11211,
            'ttl' => 60
        ];

        config()->set('cache.default', 'memcached');

        config()->set('cache.memcached', $params);

        $cache = CacheFactory::get();

        $this->assertInstanceOf(MemcachedAdapter::class, $cache->getAdapter());
    }

    public function testCacheFactoryRedisAdapter()
    {
        $params = [
            'prefix' => 'test',
            'host' => '127.0.0.1',
            'port' => 6379,
            'ttl' => 60
        ];


        config()->set('cache.default', 'redis');

        config()->set('cache.redis', $params);

        $cache = CacheFactory::get();

        $this->assertInstanceOf(RedisAdapter::class, $cache->getAdapter());
    }

    public function testCacheFactoryInvalidTypeAdapter()
    {
        config()->set('cache.default', 'invalid');

        $this->expectException(CacheException::class);

        $this->expectExceptionMessage('The adapter `invalid` is not supported`');

        CacheFactory::get();
    }

    public function testCacheFactoryReturnsSameInstance()
    {
        $cache1 = CacheFactory::get();
        $cache2 = CacheFactory::get();

        $this->assertSame($cache1, $cache2);
    }
}