<?php

namespace Quantum\Tests\Unit\Module;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Module\ModuleLoader;
use Quantum\Di\Di;
use Closure;

class ModuleLoaderTest extends AppTestCase
{
    public $moduleLoader;
    public function setUp(): void
    {
        parent::setUp();

        if (!Di::isRegistered(ModuleLoader::class)) {
            Di::register(ModuleLoader::class);
        }

        $this->moduleLoader = Di::get(ModuleLoader::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testGetInstance(): void
    {
        $this->assertInstanceOf(ModuleLoader::class, $this->moduleLoader);
    }

    public function testLoadModulesRoutes(): void
    {
        $modulesRoutes = $this->moduleLoader->loadModulesRoutes();

        $this->assertNotEmpty($modulesRoutes);

        $this->assertIsArray($modulesRoutes);

        $this->assertArrayHasKey('Test', $modulesRoutes);

        $this->assertInstanceOf(Closure::class, $modulesRoutes['Test']);
    }

    public function testLoadModulesDependencies(): void
    {
        $deps = $this->moduleLoader->loadModulesDependencies();

        $this->assertIsArray($deps);

        $this->assertArrayHasKey(\Quantum\Transformer\Contracts\TransformerInterface::class, $deps);
        $this->assertSame(\Quantum\Tests\_root\modules\Test\Transformers\PostTransformer::class, $deps[\Quantum\Transformer\Contracts\TransformerInterface::class]);

        $this->assertArrayHasKey(\Quantum\Storage\Contracts\TokenServiceInterface::class, $deps);
        $this->assertSame(\Quantum\Tests\_root\shared\Services\TokenService::class, $deps[\Quantum\Storage\Contracts\TokenServiceInterface::class]);
    }

    public function testGetModuleConfigs(): void
    {
        $configs = $this->moduleLoader->getModuleConfigs();

        $this->assertIsArray($configs);
        $this->assertArrayHasKey('Test', $configs);
        $this->assertArrayNotHasKey('Mame', $configs);
    }
}
