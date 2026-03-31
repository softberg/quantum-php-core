<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Quantum\Console\Commands\RouteListCommand;
use Quantum\Tests\Unit\AppTestCase;

class RouteListCommandTest extends AppTestCase
{
    private RouteListCommand $command;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new RouteListCommand();
    }

    public function testCommandMetadata(): void
    {
        $this->assertSame('route:list', $this->command->getName());
        $this->assertSame('Display all registered routes', $this->command->getDescription());
    }

    public function testCommandOptionsAreRegistered(): void
    {
        $definition = $this->command->getDefinition();

        $this->assertTrue($definition->hasOption('module'));
        $this->assertFalse($definition->getOption('module')->isValueRequired());
    }
}
