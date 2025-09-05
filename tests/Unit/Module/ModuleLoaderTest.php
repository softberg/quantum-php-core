<?php

namespace Module;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Module\ModuleLoader;
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

    public function testLoadModulesRoutes()
    {
        $modulesRoutes = $this->moduleLoader->loadModulesRoutes();

        Router::setRoutes($modulesRoutes);

        $this->assertNotEmpty(Router::getRoutes());

        $this->assertIsArray(Router::getRoutes());

        $this->assertCount(2, Router::getRoutes());
    }

    public function testLoadModulesDependencies()
    {
        $deps = $this->moduleLoader->loadModulesDependencies();

        $this->assertIsArray($deps);

        $this->assertArrayHasKey("Quantum\Libraries\Transformer\Transformer", $deps);
        $this->assertSame("Shared\Transformers\PostTransformer", $deps["Quantum\Libraries\Transformer\Transformer"]);

        $this->assertArrayHasKey("Quantum\Service\QtService", $deps);
        $this->assertSame("Shared\Services\TokenService", $deps["Quantum\Service\QtService"]);
    }

    public function testGetModuleConfigs()
    {
        $configs = $this->moduleLoader->getModuleConfigs();

        $this->assertIsArray($configs);
        $this->assertArrayHasKey('Test', $configs);
        $this->assertArrayNotHasKey('Mame', $configs);
    }
}