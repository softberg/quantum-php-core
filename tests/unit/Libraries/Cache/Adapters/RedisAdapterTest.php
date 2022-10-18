<?php

namespace Libraries\Cache\Adapters;

use Quantum\Libraries\Cache\Adapters\RedisAdapter;
use PHPUnit\Framework\TestCase;
use Quantum\Di\Di;
use Quantum\App;

class RedisAdapterTest extends TestCase
{

    private $redis;

    public function setUp(): void
    {
        App::loadCoreFunctions(dirname(__DIR__, 5) . DS . 'src' . DS . 'Helpers');

        App::setBaseDir(dirname(__DIR__, 3) . DS . '_root');

        Di::loadDefinitions();

        $params = [
            'prefix' => 'test',
            'host' => '127.0.0.1',
            'port' => 6379,
            'ttl' => 60
        ];

        $this->redis = new RedisAdapter($params);
    }

    public function tearDown(): void
    {
        $this->redis->clear();
    }

    public function testRedisAdapterSetGetDelete()
    {

        $this->assertNull($this->redis->get('test'));

        $this->assertNotNull($this->redis->get('test', 'Some default value'));

        $this->assertEquals('Some default value', $this->redis->get('test', 'Some default value'));

        $this->redis->set('test', 'Test value');

        $this->assertNotNull($this->redis->get('test'));

        $this->assertEquals('Test value', $this->redis->get('test'));

        $this->redis->delete('test');

        $this->assertNull($this->redis->get('test'));
    }

    public function testRedisAdapterHas()
    {
        $this->assertFalse($this->redis->has('test'));

        $this->redis->set('test', 'Some value');

        $this->assertTrue($this->redis->has('test'));
    }

    public function testRedisAdapterGetMultiple()
    {
        $cacheItems = $this->redis->getMultiple(['test1', 'test2']);

        $this->assertIsArray($cacheItems);

        $this->assertArrayHasKey('test1', $cacheItems);

        $this->assertNull($cacheItems['test1']);

        $cacheItems = $this->redis->getMultiple(['test1', 'test2'], 'Default value for all');

        $this->assertIsArray($cacheItems);

        $this->assertArrayHasKey('test1', $cacheItems);

        $this->assertNotNull($cacheItems['test1']);

        $this->assertEquals('Default value for all', $cacheItems['test1']);

        $this->redis->set('test1', 'Test one');

        $this->redis->set('test2', 'Test two');

        $cacheItems = $this->redis->getMultiple(['test1', 'test2']);

        $this->assertIsArray($cacheItems);

        $this->assertEquals('Test one', $cacheItems['test1']);
    }

    public function testRedisAdapterSetMultiple()
    {
        $this->assertFalse($this->redis->has('test1'));

        $this->assertFalse($this->redis->has('test2'));

        $this->redis->setMultiple(['test1' => 'Test value one', 'test2' => 'Test value two']);

        $this->assertTrue($this->redis->has('test1'));

        $this->assertEquals('Test value one', $this->redis->get('test1'));

        $this->assertTrue($this->redis->has('test2'));

        $this->assertEquals('Test value two', $this->redis->get('test2'));
    }

    public function testRedisAdapterDeleteMultiple()
    {
        $this->redis->setMultiple(['test1' => 'Test value one', 'test2' => 'Test value two']);

        $this->assertTrue($this->redis->has('test1'));

        $this->assertTrue($this->redis->has('test2'));

        $this->redis->deleteMultiple(['test1', 'test2']);

        $this->assertFalse($this->redis->has('test1'));

        $this->assertFalse($this->redis->has('test2'));
    }

    public function testRedisAdapterClear()
    {
        $this->redis->setMultiple(['test1' => 'Test value one', 'test2' => 'Test value two']);

        $this->assertTrue($this->redis->has('test1'));

        $this->assertTrue($this->redis->has('test2'));

        $this->redis->clear();

        $this->assertFalse($this->redis->has('test1'));

        $this->assertFalse($this->redis->has('test2'));
    }

    public function testRedisAdapterExpired()
    {
        $params = [
            'prefix' => 'test',
            'host' => '127.0.0.1',
            'port' => 6379,
            'ttl' => 1
        ];

        $redis = new RedisAdapter($params);

        $redis->set('test', 'Test value');
        
        sleep(2);

        $this->assertNull($redis->get('test'));
    }

}
