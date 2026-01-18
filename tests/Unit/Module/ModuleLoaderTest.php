<?php

namespace Module;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Module\ModuleLoader;
use Closure;

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

        $this->assertNotEmpty($modulesRoutes);

        $this->assertIsArray($modulesRoutes);

        $this->assertArrayHasKey('Test', $modulesRoutes);

        $this->assertInstanceOf(Closure::class, $modulesRoutes['Test']);
    }

    public function testLoadModulesDependencies()
    {
        $deps = $this->moduleLoader->loadModulesDependencies();

        $this->assertIsArray($deps);

        $this->assertArrayHasKey(\Quantum\Libraries\Transformer\Contracts\TransformerInterface::class, $deps);
        $this->assertSame(\Quantum\Tests\_root\modules\Test\Transformers\PostTransformer::class, $deps[\Quantum\Libraries\Transformer\Contracts\TransformerInterface::class]);

        $this->assertArrayHasKey(\Quantum\Libraries\Storage\Contracts\TokenServiceInterface::class, $deps);
        $this->assertSame(\Quantum\Tests\_root\shared\Services\TokenService::class, $deps[\Quantum\Libraries\Storage\Contracts\TokenServiceInterface::class]);
    }

    public function testGetModuleConfigs()
    {
        $configs = $this->moduleLoader->getModuleConfigs();

        $this->assertIsArray($configs);
        $this->assertArrayHasKey('Test', $configs);
        $this->assertArrayNotHasKey('Mame', $configs);
    }
}
