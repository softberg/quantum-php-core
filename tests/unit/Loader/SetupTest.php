<?php


namespace Quantum\Test\Unit;

use PHPUnit\Framework\TestCase;
use Quantum\Exceptions\ConfigException;
use Quantum\Loader\Setup;

class SetupTest extends TestCase
{

    private $setup;

    public function setUp(): void
    {
        $this->setup = new Setup();
    }

    public function tearDown(): void
    {
        unset($this->setup);
    }

    public function testConstructor()
    {
        $setup = new Setup('config', 'database');

        $this->assertEquals('config', $setup->getEnv());

        $this->assertEquals('database', $setup->getFilename());

        $this->assertEquals(false, $setup->getHierarchy());

        $this->assertEquals(ConfigException::CONFIG_FILE_NOT_FOUND, $setup->getExceptionMessage());

    }

    public function testSetGetEnv()
    {
        $this->setup->setEnv('config');

        $this->assertEquals('config', $this->setup->getEnv());
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

