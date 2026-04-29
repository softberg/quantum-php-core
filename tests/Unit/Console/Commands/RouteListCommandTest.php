<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Symfony\Component\Console\Tester\CommandTester;
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

    public function testExecRendersRoutesTable(): void
    {
        $tester = new CommandTester($this->command);
        $tester->execute([]);

        $output = $tester->getDisplay();
        $this->assertStringContainsString('Routes', $output);
        $this->assertStringContainsString('MODULE', $output);
        $this->assertStringContainsString('URI', $output);
    }

    public function testExecShowsErrorForUnknownModuleFilter(): void
    {
        $tester = new CommandTester($this->command);
        $tester->execute([
            '--module' => 'Nope',
        ]);

        $this->assertStringContainsString('The module is not found', $tester->getDisplay());
    }
}
