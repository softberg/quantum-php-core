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
        if (!$this->fs->isDirectory($openApiAssets)) {
            mkdir($openApiAssets, 0777, true);
        }
        file_put_contents($openApiAssets . DS . 'index.css', '/* stub */');

        $tester = new CommandTester($this->command);
        $tester->execute([
            'module' => 'MissingModule',
        ]);

        $this->assertStringContainsString('The module `MissingModule` not found', $tester->getDisplay());

        @unlink($openApiAssets . DS . 'index.css');
        @rmdir($openApiAssets);
    }
}
