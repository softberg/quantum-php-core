<?php

namespace Quantum\Tests\Unit\Config;

use Quantum\Config\Exceptions\ConfigException;
use Quantum\Loader\Exceptions\LoaderException;
use Quantum\Tests\Unit\AppTestCase;
use Dflydev\DotAccessData\Data;
use Quantum\Config\Config;
use Quantum\Loader\Setup;

class ConfigTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config()->flush();
    }

    public function testConfigLoad()
    {
        $config = Config::getInstance();

        $this->assertEmpty($config->all());

        $config->load(new Setup('config', 'app'));

        $this->assertNotEmpty($config->all());

        $this->assertInstanceOf(Data::class, $config->all());
    }

    public function testConfigImport()
    {
        $config = Config::getInstance();

        $config->load(new Setup('config', 'app'));

        $this->assertNull($config->get('database.default'));

        $config->import(new Setup('config', 'database'));

        $this->assertNotNull($config->get('database.default'));

        $this->assertEquals('sqlite', $config->get('database.default'));
    }

    public function testImportingNonExistingConfigFile()
    {
        $this->expectException(LoaderException::class);

        $this->expectExceptionMessage('File `config' . DS . 'somefile` not found!');

        Config::getInstance()->import(new Setup('config', 'somefile'));
    }

    public function testCollisionAtImporting()
    {
        $config = Config::getInstance();

        $config->import(new Setup('config', 'app'));

        $this->expectException(ConfigException::class);

        $this->expectExceptionMessage('Config key `app` is already in use');

        $config->import(new Setup('config', 'app'));
    }

    public function testConfigHas()
    {
        $config = Config::getInstance();

        $config->import(new Setup('config', 'app'));

        $this->assertTrue($config->has('app.debug'));

        $this->assertTrue($config->has('app.test'));

        $this->assertFalse($config->has('app.none'));
    }

    public function testConfigGet()
    {
        $config = Config::getInstance();

        $config->import(new Setup('config', 'lang'));

        $this->assertIsArray($config->get('lang.supported'));

        $this->assertEquals('Default Value', $config->get('not-exists', 'Default Value'));

        $this->assertNull($config->get('not-exists'));
    }

    public function testConfigSet()
    {
        $config = Config::getInstance();

        $this->assertFalse($config->has('new-value'));

        $config->set('new-value', 'New Value');

        $this->assertTrue($config->has('new-value'));

        $this->assertEquals('New Value', $config->get('new-value'));

        $config->set('other.nested', 'Nested Value');

        $this->assertTrue($config->has('other.nested'));

        $this->assertEquals('Nested Value', $config->get('other.nested'));
    }

    public function testConfigDelete()
    {
        $config = Config::getInstance();

        $config->import(new Setup('config', 'app'));

        $this->assertNotNull($config->get('app.test'));

        $config->delete('app.test');

        $this->assertFalse($config->has('app.test'));

        $this->assertNull($config->get('app.test'));
    }

    public function testConfigFlush()
    {
        $config = Config::getInstance();

        $config->import(new Setup('config', 'app'));

        $this->assertNotEmpty($config->all());

        $config->flush();

        $this->assertEmpty($config->all());
    }
}
