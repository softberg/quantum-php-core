<?php

namespace Quantum\Tests\Unit\Libraries\Cache;

use Quantum\Libraries\Cache\Exceptions\CacheException;
use Quantum\Libraries\Cache\Adapters\FileAdapter;
use Psr\SimpleCache\CacheInterface;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Libraries\Cache\Cache;

class CacheTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCacheGetAdapter()
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

    public function testCacheCallingValidMethod()
    {
        $params = [
            'prefix' => 'test',
            'path' => base_dir() . DS . 'cache' . DS . 'data',
            'ttl' => 60
        ];

        $cache = new Cache(new FileAdapter($params));

        $this->assertFalse($cache->has('test'));

        $this->assertNull($cache->get('test'));
    }

    public function testCacheCallingInvalidMethod()
    {
        $params = [
            'prefix' => 'test',
            'path' => base_dir() . DS . 'cache' . DS . 'data',
            'ttl' => 60
        ];

        $cache = new Cache(new FileAdapter($params));

        $this->expectException(CacheException::class);

        $this->expectExceptionMessage('The method `callingInvalidMethod` is not supported for `' . FileAdapter::class . '`');

        $cache->callingInvalidMethod();
    }
}