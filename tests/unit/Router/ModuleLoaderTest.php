<?php

namespace Quantum\Tests\Router;

use Quantum\Router\ModuleLoader;
use Quantum\Tests\AppTestCase;
use Quantum\Router\Router;

class ModuleLoaderTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->moduleLoader = ModuleLoader::getInstance();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testGetInstance()
    {
        $moduleLoader = ModuleLoader::getInstance();
        $this->assertInstanceOf(ModuleLoader::class, $moduleLoader);
    }

    public function testLoadModulesRoutesWithEnabledModules()
    {
        $this->moduleLoader->loadModulesRoutes();

        $this->assertNotEmpty(Router::getRoutes());

        $this->assertIsArray(Router::getRoutes());

        $this->assertCount(2, Router::getRoutes());
    }


}