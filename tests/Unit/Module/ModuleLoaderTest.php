<?php

namespace Module;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Module\ModuleLoader;
use Quantum\Router\Router;

class ModuleLoaderTest extends AppTestCase
{
    public $moduleLoader;
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

        $this->assertArrayHasKey("Quantum\Libraries\Transformer\Contracts\TransformerInterface", $deps);
        $this->assertSame("Quantum\Tests\_root\modules\Test\Transformers\PostTransformer", $deps["Quantum\Libraries\Transformer\Contracts\TransformerInterface"]);

        $this->assertArrayHasKey("Quantum\Libraries\Storage\Contracts\TokenServiceInterface", $deps);
        $this->assertSame("Quantum\Tests\_root\shared\Services\TokenService", $deps["Quantum\Libraries\Storage\Contracts\TokenServiceInterface"]);
    }

    public function testGetModuleConfigs()
    {
        $configs = $this->moduleLoader->getModuleConfigs();

        $this->assertIsArray($configs);
        $this->assertArrayHasKey('Test', $configs);
        $this->assertArrayNotHasKey('Mame', $configs);
    }
}