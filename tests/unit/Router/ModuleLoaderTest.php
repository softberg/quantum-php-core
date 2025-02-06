<?php

namespace Quantum\Tests\Unit\Router;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\ModuleLoader;
use Quantum\Router\Router;

class ModuleLoaderTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setPrivateProperty(ModuleLoader::class, 'instance', null);

        $this->moduleLoader = ModuleLoader::getInstance();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testGetInstance()
    {
        $this->assertInstanceOf(ModuleLoader::class, $this->moduleLoader);
    }

    public function testLoadModulesRoutesWithEnabledModules()
    {
        $this->moduleLoader->loadModulesRoutes();

        $this->assertNotEmpty(Router::getRoutes());

        $this->assertIsArray(Router::getRoutes());

        $this->assertCount(2, Router::getRoutes());
    }
}