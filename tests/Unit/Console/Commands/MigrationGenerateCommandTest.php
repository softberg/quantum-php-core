<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Quantum\Console\Commands\MigrationGenerateCommand;
use Quantum\Tests\Unit\AppTestCase;

class MigrationGenerateCommandTest extends AppTestCase
{
    private MigrationGenerateCommand $command;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new MigrationGenerateCommand();
    }

    public function testCommandMetadata(): void
    {
        $this->assertSame('migration:generate', $this->command->getName());
        $this->assertSame('Generates new migration file', $this->command->getDescription());
    }

    public function testCommandArgumentsAreRegistered(): void
    {
        $definition = $this->command->getDefinition();

        $this->assertTrue($definition->hasArgument('action'));
        $this->assertTrue($definition->hasArgument('table'));
        $this->assertTrue($definition->getArgument('action')->isRequired());
        $this->assertTrue($definition->getArgument('table')->isRequired());
    }
}
