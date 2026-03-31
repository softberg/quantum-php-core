<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Quantum\Console\Commands\InstallToolkitCommand;
use Quantum\Tests\Unit\AppTestCase;

class InstallToolkitCommandTest extends AppTestCase
{
    private InstallToolkitCommand $command;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new InstallToolkitCommand();
    }

    public function testCommandMetadata(): void
    {
        $this->assertSame('install:toolkit', $this->command->getName());
        $this->assertSame('Installs toolkit', $this->command->getDescription());
        $this->assertSame('The command will install Toolkit and its assets into your project', $this->command->getHelp());
    }

    public function testCommandArgumentsAreRegistered(): void
    {
        $definition = $this->command->getDefinition();

        $this->assertTrue($definition->hasArgument('username'));
        $this->assertTrue($definition->hasArgument('password'));
        $this->assertTrue($definition->getArgument('username')->isRequired());
        $this->assertTrue($definition->getArgument('password')->isRequired());
    }
}
