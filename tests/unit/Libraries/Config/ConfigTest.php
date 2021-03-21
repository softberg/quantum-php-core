<?php

namespace Quantum\Test\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use Quantum\Libraries\Config\Config;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Loader\Loader;

class ConfigTest extends TestCase
{

    private $loaderMock;
    private $config;
    private $configData = [
        'langs' => ['en', 'es'],
        'lang_default' => 'en',
        'debug' => 'DEBUG',
        'test' => 'Testing'
    ];
    private $otherConfigData = [
        'more' => 'info',
        'preview' => 'yes',
    ];

    public function setUp(): void
    {
        $loader = new Loader(new FileSystem);

        $loader->loadDir(dirname(__DIR__, 4) . DS . 'src' . DS . 'Helpers' . DS . 'functions');

        $this->loaderMock = Mockery::mock('Quantum\Loader\Loader');

        $this->loaderMock->shouldReceive('setup')->andReturn($this->loaderMock);

        $this->config = Config::getInstance();

        $this->config->flush();
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testConfigLoad()
    {
        $this->assertEmpty($this->config->all());

        $this->loaderMock->shouldReceive('load')->andReturn($this->configData);

        $this->config->load($this->loaderMock);

        $this->assertNotEmpty($this->config->all());

        $this->assertIsArray($this->config->all());
    }

    public function testConfigImport()
    {
        $this->loaderMock->shouldReceive('load')->andReturn($this->otherConfigData);

        $this->config->import($this->loaderMock, 'other');

        $this->assertEquals('info', $this->config->get('other.more'));
    }

    public function testConfigHas()
    {
        $this->loaderMock->shouldReceive('load')->andReturn($this->configData);

        $this->config->load($this->loaderMock);

        $this->assertFalse($this->config->has('foo'));

        $this->assertTrue($this->config->has('test'));
    }

    public function testConfigGet()
    {
        $this->loaderMock->shouldReceive('load')->andReturn($this->configData);

        $this->config->load($this->loaderMock);
        
        $this->assertEquals('Testing', $this->config->get('test'));

        $this->assertEquals('Default Value', $this->config->get('not-exists', 'Default Value'));

        $this->assertNull($this->config->get('not-exists'));
    }

    public function testConfigSet()
    {
        $this->loaderMock->shouldReceive('load')->andReturn($this->configData);

        $this->config->load($this->loaderMock);
        
        $this->assertNull($this->config->get('new-value'));

        $this->config->set('new-value', 'New Value');

        $this->assertTrue($this->config->has('new-value'));

        $this->assertEquals('New Value', $this->config->get('new-value'));

        $this->config->set('other.nested', 'Nested Value');

        $this->assertTrue($this->config->has('other.nested'));

        $this->assertEquals('Nested Value', $this->config->get('other.nested'));
    }

    public function testConfigDelete()
    {
        $this->loaderMock->shouldReceive('load')->andReturn($this->configData);

        $this->config->load($this->loaderMock);
        
        $this->assertNotNull($this->config->get('test'));

        $this->config->delete('test');

        $this->assertFalse($this->config->has('test'));

        $this->assertNull($this->config->get('test'));
    }

    public function testConfigFlush()
    {
        $this->loaderMock->shouldReceive('load')->andReturn($this->configData);

        $this->config->load($this->loaderMock);
        
        $this->assertNotEmpty($this->config->all());

        $this->assertIsArray($this->config->all());

        $this->config->flush();

        $this->assertEmpty($this->config->all());
    }

}
