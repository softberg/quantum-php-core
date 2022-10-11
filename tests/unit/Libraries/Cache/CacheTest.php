<?php

namespace Quantum\Tests\Libraries\Cache;

use Quantum\Libraries\Cache\Adapters\FileAdapter;
use Quantum\Exceptions\CacheException;
use Psr\SimpleCache\CacheInterface;
use Quantum\Libraries\Cache\Cache;
use PHPUnit\Framework\TestCase;
use Quantum\Di\Di;
use Quantum\App;

class CacheTest extends TestCase
{

    public function setUp(): void
    {
        App::loadCoreFunctions(dirname(__DIR__, 4) . DS . 'src' . DS . 'Helpers');

        App::setBaseDir(dirname(__DIR__, 2) . DS . '_root');

        Di::loadDefinitions();
    }

    public function testCacheAdapter()
    {
        $params = [
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
