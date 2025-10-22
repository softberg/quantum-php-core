<?php

namespace Quantum\Tests\Unit\Libraries\Cache\Adapters;

use Quantum\Libraries\Database\Adapters\Sleekdb\SleekDbal;
use Quantum\Libraries\Cache\Adapters\DatabaseAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Loader\Setup;

class DatabaseAdapterTest extends AppTestCase
{

    private $databaseCache;

    public function setUp(): void
    {
        parent::setUp();

        if (!config()->has('database')) {
            config()->import(new Setup('config', 'database'));
        }

        config()->set('database.default', 'sleekdb');

        SleekDbal::connect(config()->get('database.sleekdb'));

        $params = [
            'prefix' => 'test',
            'table' => 'cache',
            'ttl' => 60
        ];

        $this->databaseCache = new DatabaseAdapter($params);
    }

    public function tearDown(): void
    {
        $this->databaseCache->clear();

        SleekDbal::disconnect();
    }

    public function testDatabaseAdapterSetGetDelete()
    {

        $this->assertNull($this->databaseCache->get('test'));

        $this->assertNotNull($this->databaseCache->get('test', 'Some default value'));

        $this->assertEquals('Some default value', $this->databaseCache->get('test', 'Some default value'));

        $this->databaseCache->set('test', 'Test value');

        $this->assertNotNull($this->databaseCache->get('test'));

        $this->assertEquals('Test value', $this->databaseCache->get('test'));

        $this->databaseCache->delete('test');

        $this->assertNull($this->databaseCache->get('test'));
    }

    public function testDatabaseAdapterHas()
    {
        $this->assertFalse($this->databaseCache->has('test'));

        $this->databaseCache->set('test', 'Some value');

        $this->assertTrue($this->databaseCache->has('test'));
    }

    public function testDatabaseAdapterGetMultiple()
    {
        $cacheItems = $this->databaseCache->getMultiple(['test1', 'test2']);

        $this->assertIsArray($cacheItems);

        $this->assertArrayHasKey('test1', $cacheItems);

        $this->assertNull($cacheItems['test1']);

        $cacheItems = $this->databaseCache->getMultiple(['test1', 'test2'], 'Default value for all');

        $this->assertIsArray($cacheItems);

        $this->assertArrayHasKey('test1', $cacheItems);

        $this->assertNotNull($cacheItems['test1']);

        $this->assertEquals('Default value for all', $cacheItems['test1']);

        $this->databaseCache->set('test1', 'Test one');

        $this->databaseCache->set('test2', 'Test two');

        $cacheItems = $this->databaseCache->getMultiple(['test1', 'test2']);

        $this->assertIsArray($cacheItems);

        $this->assertEquals('Test one', $cacheItems['test1']);
    }

    public function testDatabaseAdapterSetMultiple()
    {
        $this->assertFalse($this->databaseCache->has('test1'));

        $this->assertFalse($this->databaseCache->has('test2'));

        $this->databaseCache->setMultiple(['test1' => 'Test value one', 'test2' => 'Test value two']);

        $this->assertTrue($this->databaseCache->has('test1'));

        $this->assertEquals('Test value one', $this->databaseCache->get('test1'));

        $this->assertTrue($this->databaseCache->has('test2'));

        $this->assertEquals('Test value two', $this->databaseCache->get('test2'));
    }

    public function testDatabaseAdapterDeleteMultiple()
    {
        $this->databaseCache->setMultiple(['test1' => 'Test value one', 'test2' => 'Test value two']);

        $this->assertTrue($this->databaseCache->has('test1'));

        $this->assertTrue($this->databaseCache->has('test2'));

        $this->databaseCache->deleteMultiple(['test1', 'test2']);

        $this->assertFalse($this->databaseCache->has('test1'));

        $this->assertFalse($this->databaseCache->has('test2'));
    }

    public function testDatabaseAdapterClear()
    {
        $this->databaseCache->setMultiple(['test1' => 'Test value one', 'test2' => 'Test value two']);

        $this->assertTrue($this->databaseCache->has('test1'));

        $this->assertTrue($this->databaseCache->has('test2'));

        $this->databaseCache->clear();

        $this->assertFalse($this->databaseCache->has('test1'));

        $this->assertFalse($this->databaseCache->has('test2'));
    }

    public function testDatabaseAdapterExpired()
    {
        $params = [
            'prefix' => 'test',
            'table' => 'cache',
            'ttl' => -1
        ];

        $databaseCache = new DatabaseAdapter($params);

        $databaseCache->set('test', 'Test value');

        $this->assertNull($databaseCache->get('test'));
    }

}