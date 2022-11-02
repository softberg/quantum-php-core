<?php

namespace Quantum\Tests\Libraries\Cache\Adapters;

use Quantum\Libraries\Cache\Adapters\FileAdapter;
use Quantum\Tests\AppTestCase;

class FileAdapterTest extends AppTestCase
{

    private $fileCache;

    public function setUp(): void
    {
        parent::setUp();

        $params = [
            'prefix' => 'test',
            'path' => base_dir() . DS . 'cache' . DS . 'data',
            'ttl' => 60
        ];

        $this->fileCache = new FileAdapter($params);
    }

    public function tearDown(): void
    {
        $this->fileCache->clear();
    }

    public function testFileAdapterSetGetDelete()
    {
        $this->assertNull($this->fileCache->get('test'));

        $this->assertNotNull($this->fileCache->get('test', 'Some default value'));

        $this->assertEquals('Some default value', $this->fileCache->get('test', 'Some default value'));

        $this->fileCache->set('test', 'Test value');

        $this->assertNotNull($this->fileCache->get('test'));

        $this->assertEquals('Test value', $this->fileCache->get('test'));

        $this->fileCache->delete('test');

        $this->assertNull($this->fileCache->get('test'));
    }

    public function testFileAdapterHas()
    {
        $this->assertFalse($this->fileCache->has('test'));

        $this->fileCache->set('test', 'Some value');

        $this->assertTrue($this->fileCache->has('test'));
    }

    public function testFileAdapterGetMultiple()
    {
        $cacheItems = $this->fileCache->getMultiple(['test1', 'test2']);

        $this->assertIsArray($cacheItems);

        $this->assertArrayHasKey('test1', $cacheItems);

        $this->assertNull($cacheItems['test1']);

        $cacheItems = $this->fileCache->getMultiple(['test1', 'test2'], 'Default value for all');

        $this->assertIsArray($cacheItems);

        $this->assertArrayHasKey('test1', $cacheItems);

        $this->assertNotNull($cacheItems['test1']);

        $this->assertEquals('Default value for all', $cacheItems['test1']);
    }

    public function testFileAdapterSetMultiple()
    {
        $this->assertFalse($this->fileCache->has('test1'));

        $this->assertFalse($this->fileCache->has('test2'));

        $this->fileCache->setMultiple(['test1' => 'Test value one', 'test2' => 'Test value two']);

        $this->assertTrue($this->fileCache->has('test1'));

        $this->assertEquals('Test value one', $this->fileCache->get('test1'));

        $this->assertTrue($this->fileCache->has('test2'));

        $this->assertEquals('Test value two', $this->fileCache->get('test2'));
    }

    public function testFileAdapterDeleteMultiple()
    {
        $this->fileCache->setMultiple(['test1' => 'Test value one', 'test2' => 'Test value two']);

        $this->assertTrue($this->fileCache->has('test1'));

        $this->assertTrue($this->fileCache->has('test2'));

        $this->fileCache->deleteMultiple(['test1', 'test2']);

        $this->assertFalse($this->fileCache->has('test1'));

        $this->assertFalse($this->fileCache->has('test2'));
    }

    public function testFileAdapterClear()
    {
        $this->fileCache->setMultiple(['test1' => 'Test value one', 'test2' => 'Test value two']);

        $this->assertTrue($this->fileCache->has('test1'));

        $this->assertTrue($this->fileCache->has('test2'));

        $this->fileCache->clear();

        $this->assertFalse($this->fileCache->has('test1'));

        $this->assertFalse($this->fileCache->has('test2'));
    }

    public function testFileAdapterExpired()
    {
        $params = [
            'prefix' => 'test',
            'path' => base_dir() . DS . 'cache' . DS . 'data',
            'ttl' => -1
        ];

        $fileCache = new FileAdapter($params);

        $fileCache->set('test', 'Test value');

        $this->assertNull($fileCache->get('test'));
    }

}
