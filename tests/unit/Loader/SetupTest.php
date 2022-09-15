<?php

namespace Quantum\Tests\Loader;

use PHPUnit\Framework\TestCase;
use Quantum\Exceptions\ConfigException;
use Quantum\Loader\Setup;
use Quantum\Di\Di;
use Quantum\App;

class SetupTest extends TestCase
{

    private $setup;

    public function setUp(): void
    {
        App::loadCoreFunctions(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers');

        App::setBaseDir(dirname(__DIR__) . DS . '_root');

        Di::loadDefinitions();

        $this->setup = new Setup();
    }

    public function tearDown(): void
    {
        unset($this->setup);
    }

    public function testConstructor()
    {
        $setup = new Setup('config', 'database');

        $this->assertEquals('config', $setup->getPathPrefix());

        $this->assertEquals('database', $setup->getFilename());

        $this->assertEquals(true, $setup->getHierarchy());

        $this->assertEquals(t('exception.config_file_not_found'), $setup->getExceptionMessage());

    }

    public function testSetGetPathPrefix()
    {
        $this->setup->setPathPrefix('config');

        $this->assertEquals('config', $this->setup->getPathPrefix());
    }

    public function testSetGetFilename()
    {
        $this->setup->setFilename('users');

        $this->assertEquals('users', $this->setup->getFilename());
    }

    public function testSetGetHierarchy()
    {
        $this->setup->setHierarchy(true);

        $this->assertTrue($this->setup->getHierarchy());
    }

    public function testSetGetModule()
    {
        $this->setup->setModule('admin');

        $this->assertEquals('admin', $this->setup->getModule());
    }

    public function testSetGetExceptionMessage()
    {
        $this->setup->setExceptionMessage('Action not allowed');

        $this->assertEquals('Action not allowed', $this->setup->getExceptionMessage());
    }


}

