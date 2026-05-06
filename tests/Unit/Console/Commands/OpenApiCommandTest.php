<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Symfony\Component\Console\Tester\CommandTester;
use Quantum\Console\Commands\OpenApiCommand;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Router\RouteCollection;
use Quantum\Storage\FileSystem;
use ReflectionMethod;
use Quantum\Di\Di;

class OpenApiCommandTest extends AppTestCase
{
    private OpenApiCommand $command;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new OpenApiCommand();
    }

    public function testCommandMetadata(): void
    {
        $this->assertSame('install:openapi', $this->command->getName());
        $this->assertSame('Generates files for OpenApi UI', $this->command->getDescription());
        $this->assertSame('The command will publish OpenApi UI resources', $this->command->getHelp());
    }

    public function testCommandArgumentsAreRegistered(): void
    {
        $definition = $this->command->getDefinition();

        $this->assertTrue($definition->hasArgument('module'));
        $this->assertTrue($definition->getArgument('module')->isRequired());
    }

    public function testConstructorInitializesFileSystem(): void
    {
        $fs = $this->getPrivateProperty($this->command, 'fs');
        $this->assertInstanceOf(FileSystem::class, $fs);
    }

    public function testExecShowsErrorWhenModuleIsMissing(): void
    {
        $openApiAssets = assets_dir() . DS . 'OpenApiUi';
        $indexCssPath = $openApiAssets . DS . 'index.css';
        $assetsDirCreatedByTest = false;
        $indexCssExisted = false;
        $indexCssBackup = '';

        if (!$this->fs->isDirectory($openApiAssets)) {
            mkdir($openApiAssets, 0777, true);
            $assetsDirCreatedByTest = true;
        }

        if ($this->fs->exists($indexCssPath)) {
            $indexCssExisted = true;
            $indexCssBackup = (string) $this->fs->get($indexCssPath);
        }

        $this->fs->put($indexCssPath, '/* stub */');

        try {
            $tester = new CommandTester($this->command);
            $tester->execute([
                'module' => 'MissingModule',
            ]);

            $this->assertStringContainsString('The module `MissingModule` not found', $tester->getDisplay());
        } finally {
            if ($indexCssExisted) {
                $this->fs->put($indexCssPath, $indexCssBackup);
            } elseif ($this->fs->exists($indexCssPath)) {
                $this->fs->remove($indexCssPath);
            }

            if ($assetsDirCreatedByTest && $this->fs->isDirectory($openApiAssets)) {
                @rmdir($openApiAssets);
            }
        }
    }

    public function testExecSkipsRouteBuildWhenCollectionAlreadyBound(): void
    {
        if (!Di::has(RouteCollection::class)) {
            Di::set(RouteCollection::class, new RouteCollection());
        }

        $openApiAssets = assets_dir() . DS . 'OpenApiUi';
        $indexCssPath = $openApiAssets . DS . 'index.css';
        $assetsDirCreatedByTest = false;
        $indexCssExisted = false;
        $indexCssBackup = '';

        if (!$this->fs->isDirectory($openApiAssets)) {
            mkdir($openApiAssets, 0777, true);
            $assetsDirCreatedByTest = true;
        }

        if ($this->fs->exists($indexCssPath)) {
            $indexCssExisted = true;
            $indexCssBackup = (string) $this->fs->get($indexCssPath);
        }

        $this->fs->put($indexCssPath, '/* stub */');

        try {
            $tester = new CommandTester($this->command);
            $tester->execute([
                'module' => 'MissingModule',
            ]);

            $this->assertStringContainsString('The module `MissingModule` not found', $tester->getDisplay());
        } finally {
            if ($indexCssExisted) {
                $this->fs->put($indexCssPath, $indexCssBackup);
            } elseif ($this->fs->exists($indexCssPath)) {
                $this->fs->remove($indexCssPath);
            }

            if ($assetsDirCreatedByTest && $this->fs->isDirectory($openApiAssets)) {
                @rmdir($openApiAssets);
            }
        }
    }

    public function testOpenapiRoutesContainsModuleSpecPath(): void
    {
        $method = new ReflectionMethod($this->command, 'openapiRoutes');
        $method->setAccessible(true);

        $routes = $method->invoke($this->command, 'Blog');

        $this->assertStringContainsString('"openapi"', $routes);
        $this->assertStringContainsString('Blog', $routes);
        $this->assertTrue(
            strpos($routes, 'resources/openapi/spec.json') !== false
            || strpos($routes, 'resources\\openapi\\spec.json') !== false
        );
    }

    public function testCopyResourcesSkipsExcludedFiles(): void
    {
        $sourceDir = base_dir() . DS . 'var' . DS . 'tmp_openapi_src_' . uniqid();
        $targetDir = base_dir() . DS . 'var' . DS . 'tmp_openapi_dst_' . uniqid();

        mkdir($sourceDir, 0777, true);
        mkdir($targetDir, 0777, true);

        file_put_contents($sourceDir . DS . 'swagger-ui.css', 'body{}');
        file_put_contents($sourceDir . DS . 'index.html', '<html></html>');

        $this->setPrivateProperty($this->command, 'vendorOpenApiFolderPath', $sourceDir);
        $this->setPrivateProperty($this->command, 'publicOpenApiFolderPath', $targetDir);

        $method = new ReflectionMethod($this->command, 'copyResources');
        $method->setAccessible(true);
        $method->invoke($this->command);

        $this->assertFileExists($targetDir . DS . 'swagger-ui.css');
        $this->assertFileDoesNotExist($targetDir . DS . 'index.html');

        @unlink($targetDir . DS . 'swagger-ui.css');
        @rmdir($targetDir);
        @unlink($sourceDir . DS . 'swagger-ui.css');
        @unlink($sourceDir . DS . 'index.html');
        @rmdir($sourceDir);
    }

    public function testGenerateOpenapiSpecificationCreatesSpecFile(): void
    {
        $command = new class () extends OpenApiCommand {
            public function info(string $message): void
            {
            }

            public function error(string $message): void
            {
            }
        };

        $moduleName = 'OpenApiSpec' . uniqid();
        $annotationDir = modules_dir() . DS . $moduleName . DS . 'Controllers' . DS . 'OpenApi';
        $specDir = modules_dir() . DS . $moduleName . DS . 'resources' . DS . 'openapi';
        $specPath = $specDir . DS . 'spec.json';

        mkdir($annotationDir, 0777, true);
        mkdir($specDir, 0777, true);
        file_put_contents($annotationDir . DS . 'SpecController.php', "<?php\n/**\n * @OA\\Info(title=\"Spec\", version=\"1.0.0\")\n */\nclass SpecController {}\n");

        try {
            $method = new ReflectionMethod($command, 'generateOpenapiSpecification');
            $method->setAccessible(true);
            $method->invoke($command, $moduleName);

            $this->assertFileExists($specPath);
            $this->assertNotSame('', trim((string) file_get_contents($specPath)));
        } finally {
            @unlink($annotationDir . DS . 'SpecController.php');
            @rmdir($annotationDir);
            @unlink($specPath);
            @rmdir($specDir);
            @rmdir(modules_dir() . DS . $moduleName . DS . 'Controllers');
            @rmdir(modules_dir() . DS . $moduleName . DS . 'resources');
            @rmdir(modules_dir() . DS . $moduleName);
        }
    }
}
