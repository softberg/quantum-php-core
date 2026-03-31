<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Quantum\Console\Commands\OpenApiCommand;
use Quantum\Storage\FileSystem;
use Quantum\Tests\Unit\AppTestCase;

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
}
