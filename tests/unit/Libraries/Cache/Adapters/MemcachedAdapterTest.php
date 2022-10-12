<?php

namespace Libraries\Cache\Adapters;

use Quantum\Libraries\Cache\Adapters\MemecachedAdapter;
use PHPUnit\Framework\TestCase;
use Quantum\Di\Di;
use Quantum\App;

class MemcachedAdapterTest extends TestCase
{

    private $memCached;

    public function setUp(): void
    {
        App::loadCoreFunctions(dirname(__DIR__, 5) . DS . 'src' . DS . 'Helpers');

        App::setBaseDir(dirname(__DIR__, 3) . DS . '_root');

        Di::loadDefinitions();

        $params = [
            'host' => '127.0.0.1',
            'port' => 11211,
            'ttl' => 60
        ];

        $this->memCached = new MemecachedAdapter($params);
    }

    public function tearDown(): void
    {
        $this->memCached->clear();
    }

    public function testMemcachedAdapterSetGetDelete()
    {

        $this->assertNull($this->memCached->get('test'));

        $this->assertNotNull($this->memCached->get('test', 'Some default value'));

        $this->assertEquals('Some default value', $this->memCached->get('test', 'Some default value'));

        $this->memCached->set('test', 'Test value');

        $this->assertNotNull($this->memCached->get('test'));

        $this->assertEquals('Test value', $this->memCached->get('test'));

        $this->memCached->delete('test');

        $this->assertNull($this->memCached->get('test'));
    }

    public function testMemcachedAdapterHas()
    {
        $this->assertFalse($this->memCached->has('test'));

        $this->memCached->set('test', 'Some value');

        $this->assertTrue($this->memCached->has('test'));
    }

    public function testMemcachedAdapterGetMultiple()
    {
        $cacheItems = $this->memCached->getMultiple(['test1', 'test2']);

        $this->assertIsArray($cacheItems);

        $this->assertArrayHasKey('test1', $cacheItems);

        $this->assertNull($cacheItems['test1']);

        $cacheItems = $this->memCached->getMultiple(['test1', 'test2'], 'Default value for all');

        $this->assertIsArray($cacheItems);

        $this->assertArrayHasKey('test1', $cacheItems);

        $this->assertNotNull($cacheItems['test1']);

        $this->assertEquals('Default value for all', $cacheItems['test1']);

        $this->memCached->set('test1', 'Test one');

        $this->memCached->set('test2', 'Test two');

        $cacheItems = $this->memCached->getMultiple(['test1', 'test2']);

        $this->assertIsArray($cacheItems);

        $this->assertEquals('Test one', $cacheItems['test1']);
    }


    public function testMemcachedAdapterSetMultiple()
    {
        $this->assertFalse($this->memCached->has('test1'));

        $this->assertFalse($this->memCached->has('test2'));

        $this->memCached->setMultiple(['test1' => 'Test value one', 'test2' => 'Test value two']);

        $this->assertTrue($this->memCached->has('test1'));

        $this->assertEquals('Test value one', $this->memCached->get('test1'));

        $this->assertTrue($this->memCached->has('test2'));

        $this->assertEquals('Test value two', $this->memCached->get('test2'));
    }

    public function testMemcachedAdapterDeleteMultiple()
    {
        $this->memCached->setMultiple(['test1' => 'Test value one', 'test2' => 'Test value two']);

        $this->assertTrue($this->memCached->has('test1'));

        $this->assertTrue($this->memCached->has('test2'));

        $this->memCached->deleteMultiple(['test1', 'test2']);

        $this->assertFalse($this->memCached->has('test1'));

        $this->assertFalse($this->memCached->has('test2'));
    }

    public function testMemcachedAdapterClear()
    {
        $this->memCached->setMultiple(['test1' => 'Test value one', 'test2' => 'Test value two']);

        $this->assertTrue($this->memCached->has('test1'));

        $this->assertTrue($this->memCached->has('test2'));

        $this->memCached->clear();

        $this->assertFalse($this->memCached->has('test1'));

        $this->assertFalse($this->memCached->has('test2'));
    }

    public function testMemcachedAdapterExpired()
    {
        $params = [
            'host' => '127.0.0.1',
            'port' => 11211,
            'ttl' => 1
        ];

        $memCached = new MemecachedAdapter($params);

        $memCached->set('test', 'Test value');
        
        sleep(2);

        $this->assertNull($memCached->get('test'));
    }
}
