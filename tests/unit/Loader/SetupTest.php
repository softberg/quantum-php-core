<?php

namespace Quantum\Tests\Unit\Loader;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Loader\Setup;

class SetupTest extends AppTestCase
{

    private $setup;

    public function setUp(): void
    {
        parent::setUp();

        $this->setup = new Setup();
    }

    public function tearDown(): void
    {
        unset($this->setup);
    }

    public function testSetupConstructor()
    {
        $setup = new Setup('config', 'database');

        $this->assertEquals('config', $setup->getPathPrefix());

        $this->assertEquals('database', $setup->getFilename());

        $this->assertEquals(true, $setup->getHierarchy());

        $this->assertEquals('File `' . $setup->getPathPrefix() . DS . $setup->getFilename() . '` not found!', $setup->getExceptionMessage());
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
