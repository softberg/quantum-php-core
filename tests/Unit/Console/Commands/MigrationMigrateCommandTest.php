<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Quantum\Console\Commands\MigrationMigrateCommand;
use Quantum\Tests\Unit\AppTestCase;

class MigrationMigrateCommandTest extends AppTestCase
{
    private MigrationMigrateCommand $command;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new MigrationMigrateCommand();
    }

    public function testCommandMetadata(): void
    {
        $this->assertSame('migration:migrate', $this->command->getName());
        $this->assertSame('Migrates the migrations', $this->command->getDescription());
    }

    public function testCommandArgumentsAndOptionsAreRegistered(): void
    {
        $definition = $this->command->getDefinition();

        $this->assertTrue($definition->hasArgument('direction'));
        $this->assertFalse($definition->getArgument('direction')->isRequired());

        $this->assertTrue($definition->hasOption('step'));
    }
}
