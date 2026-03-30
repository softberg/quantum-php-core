<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Quantum\Console\Commands\ResourceCacheClearCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Quantum\Storage\FileSystem;
use Quantum\Tests\Unit\AppTestCase;

class ResourceCacheClearCommandTest extends AppTestCase
{
    private ResourceCacheClearCommand $command;

    private CommandTester $tester;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new ResourceCacheClearCommand();
        $this->tester = new CommandTester($this->command);
    }

    public function testCommandMetadata(): void
    {
        $this->assertSame('cache:clear', $this->command->getName());
        $this->assertSame('Clears resource cache', $this->command->getDescription());
        $this->assertSame('The command will clear the resource cache', $this->command->getHelp());
    }

    public function testCommandOptionsAreRegistered(): void
    {
        $definition = $this->command->getDefinition();

        $this->assertTrue($definition->hasOption('all'));
        $this->assertTrue($definition->hasOption('type'));
        $this->assertTrue($definition->hasOption('module'));
    }

    public function testConstructorInitializesFileSystem(): void
    {
        $fs = $this->getPrivateProperty($this->command, 'fs');
        $this->assertInstanceOf(FileSystem::class, $fs);
    }

    public function testExecShowsErrorWhenNoOptionsProvided(): void
    {
        config()->set('view_cache', ['cache_dir' => 'cache']);

        $cacheDir = base_dir() . DS . 'cache';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        $this->tester->execute([]);

        $output = $this->tester->getDisplay();
        $this->assertStringContainsString('Please specify at least one of the following options', $output);

        @rmdir($cacheDir);
    }
}
