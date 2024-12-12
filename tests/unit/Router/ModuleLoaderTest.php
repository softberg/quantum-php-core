<?php

namespace Quantum\Tests\Router;

use Quantum\Router\ModuleLoader;
use Quantum\Tests\AppTestCase;
use Quantum\Router\Router;

class ModuleLoaderTest extends AppTestCase
{
    private $moduleConfigFile;

    private $routeConfigFile;

    public function setUp(): void
    {
        parent::setUp();

        $this->moduleConfigFile = base_dir() . DS . 'shared' . DS . 'config' . DS . 'modules.php';

        $moduleConfigContent = "<?php
            return [
                'modules' => [
                    'Test' => [
                        'prefix' => 'test',
                        'enabled' => true,
                    ],
                    'Meme' => [
                        'prefix' => 'meme',
                        'enabled' => false,
                    ],
                ]
            ];";

        $this->createFile($this->moduleConfigFile, $moduleConfigContent);

        $this->routeConfigFile = modules_dir() . DS . 'test' . DS . 'Config' . DS . 'routes.php';

        $routeConfigContent = '<?php
            return function ($route) {
                $route->get("[:alpha:2]?/tests", "TestController", "tests");
                $route->get("[:alpha:2]?/test/[id=:any]", "TestController", "test");
            };
        ';

        $this->createFile($this->routeConfigFile, $routeConfigContent);

        $this->moduleLoader = ModuleLoader::getInstance();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->removeFile($this->moduleConfigFile);
        $this->removeFile($this->routeConfigFile);

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