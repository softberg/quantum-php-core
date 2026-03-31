<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Quantum\Console\Commands\ModuleGenerateCommand;
use Quantum\Tests\Unit\AppTestCase;

class ModuleGenerateCommandTest extends AppTestCase
{
    private ModuleGenerateCommand $command;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new ModuleGenerateCommand();
    }

    public function testCommandMetadata(): void
    {
        $this->assertSame('module:generate', $this->command->getName());
        $this->assertSame('Generate new module', $this->command->getDescription());
        $this->assertSame('The command will create files for new module', $this->command->getHelp());
    }

    public function testCommandArgumentsAndOptionsAreRegistered(): void
    {
        $definition = $this->command->getDefinition();

        $this->assertTrue($definition->hasArgument('module'));
        $this->assertTrue($definition->getArgument('module')->isRequired());

        $this->assertTrue($definition->hasOption('yes'));
        $this->assertTrue($definition->hasOption('template'));
        $this->assertTrue($definition->hasOption('with-assets'));
    }
}
