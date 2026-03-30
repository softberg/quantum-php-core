<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Symfony\Component\Console\Tester\CommandTester;
use Quantum\Console\Commands\DebugBarCommand;
use Quantum\Storage\FileSystem;
use Quantum\Tests\Unit\AppTestCase;

class DebugBarCommandTest extends AppTestCase
{
    private DebugBarCommand $command;

    private CommandTester $tester;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new DebugBarCommand();
        $this->tester = new CommandTester($this->command);
    }

    public function testCommandMetadata(): void
    {
        $this->assertSame('install:debugbar', $this->command->getName());
        $this->assertSame('Publishes debugbar assets', $this->command->getDescription());
        $this->assertSame('The command will publish debugbar assets', $this->command->getHelp());
    }

    public function testConstructorInitializesFileSystem(): void
    {
        $fs = $this->getPrivateProperty($this->command, 'fs');
        $this->assertInstanceOf(FileSystem::class, $fs);
    }

    public function testExecShowsErrorWhenAlreadyInstalled(): void
    {
        $assetsPath = assets_dir() . DS . 'DebugBar' . DS . 'Resources';

        mkdir($assetsPath, 0777, true);
        file_put_contents($assetsPath . DS . 'debugbar.css', '/* stub */');

        $this->tester->execute([]);

        $output = $this->tester->getDisplay();
        $this->assertStringContainsString('already installed', $output);

        @unlink($assetsPath . DS . 'debugbar.css');
        @rmdir($assetsPath);
        @rmdir(assets_dir() . DS . 'DebugBar');
    }
}
