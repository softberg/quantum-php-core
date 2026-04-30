<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Symfony\Component\Console\Tester\CommandTester;
use Quantum\Console\Commands\OpenApiCommand;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Storage\FileSystem;

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
}
