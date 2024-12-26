<?php

namespace Quantum\Tests\Libraries\Cache;

use Quantum\Libraries\Cache\Adapters\FileAdapter;
use Quantum\Libraries\Cache\CacheException;
use Psr\SimpleCache\CacheInterface;
use Quantum\Libraries\Cache\Cache;
use Quantum\Tests\AppTestCase;

class CacheTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCacheAdapter()
    {
        $params = [
            'prefix' => 'test',
            'path' => base_dir() . DS . 'cache' . DS . 'data',
            'ttl' => 60
        ];

        $cache = new Cache(new FileAdapter($params));

        $this->assertInstanceOf(FileAdapter::class, $cache->getAdapter());

        $this->assertInstanceOf(CacheInterface::class, $cache->getAdapter());
    }

    public function testAdapterMethodCall()
    {
        $params = [
            'prefix' => 'test',
            'path' => base_dir() . DS . 'cache' . DS . 'data',
            'ttl' => 60
        ];

        $cache = new Cache(new FileAdapter($params));

        $this->assertFalse($cache->has('test'));

        $this->assertNull($cache->get('test'));

        $this->expectException(CacheException::class);

        $this->expectExceptionMessage('exception.not_supported_method');

        $cache->callingSomeMethod();
    }

}
