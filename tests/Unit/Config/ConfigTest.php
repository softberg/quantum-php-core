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
    private Config $config;

    public function setUp(): void
    {
        parent::setUp();

        config()->flush();

        $this->config = new Config();
    }

    public function testConfigLoad(): void
    {
        $this->assertEmpty($this->config->all());

        $this->config->load(new Setup('config', 'app'));

        $this->assertNotEmpty($this->config->all());

        $this->assertInstanceOf(Data::class, $this->config->all());
    }

    public function testConfigImport(): void
    {
        $this->config->load(new Setup('config', 'app'));

        $this->assertNull($this->config->get('database.default'));

        $this->config->import(new Setup('config', 'database'));

        $this->assertNotNull($this->config->get('database.default'));

        $this->assertEquals('sqlite', $this->config->get('database.default'));
    }

    public function testImportingNonExistingConfigFile(): void
    {
        $this->expectException(LoaderException::class);

        $this->expectExceptionMessage('File `config' . DS . 'somefile` not found!');

        $this->config->import(new Setup('config', 'somefile'));
    }

    public function testCollisionAtImporting(): void
    {
        $this->config->import(new Setup('config', 'app'));

        $this->expectException(ConfigException::class);

        $this->expectExceptionMessage('Config key `app` is already in use');

        $this->config->import(new Setup('config', 'app'));
    }

    public function testConfigHas(): void
    {
        $this->config->import(new Setup('config', 'app'));

        $this->assertTrue($this->config->has('app.debug'));

        $this->assertTrue($this->config->has('app.test'));

        $this->assertFalse($this->config->has('app.none'));
    }

    public function testConfigGet(): void
    {
        $this->config->import(new Setup('config', 'lang'));

        $this->assertIsArray($this->config->get('lang.supported'));

        $this->assertEquals('Default Value', $this->config->get('not-exists', 'Default Value'));

        $this->assertNull($this->config->get('not-exists'));
    }

    public function testConfigSet(): void
    {
        $this->assertFalse($this->config->has('new-value'));

        $this->config->set('new-value', 'New Value');

        $this->assertTrue($this->config->has('new-value'));

        $this->assertEquals('New Value', $this->config->get('new-value'));

        $this->config->set('other.nested', 'Nested Value');

        $this->assertTrue($this->config->has('other.nested'));

        $this->assertEquals('Nested Value', $this->config->get('other.nested'));
    }

    public function testConfigDelete(): void
    {
        $this->config->import(new Setup('config', 'app'));

        $this->assertNotNull($this->config->get('app.test'));

        $this->config->delete('app.test');

        $this->assertFalse($this->config->has('app.test'));

        $this->assertNull($this->config->get('app.test'));
    }

    public function testConfigFlush(): void
    {
        $this->config->import(new Setup('config', 'app'));

        $this->assertNotEmpty($this->config->all());

        $this->config->flush();

        $this->assertEmpty($this->config->all());
    }
}
