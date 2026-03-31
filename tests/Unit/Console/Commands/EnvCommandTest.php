<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Symfony\Component\Console\Tester\CommandTester;
use Quantum\Console\Commands\EnvCommand;
use Quantum\Tests\Unit\AppTestCase;

class EnvCommandTest extends AppTestCase
{
    private EnvCommand $command;

    private CommandTester $tester;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new EnvCommand();
        $this->tester = new CommandTester($this->command);
    }

    public function testCommandMetadata(): void
    {
        $this->assertSame('core:env', $this->command->getName());
        $this->assertSame('Generates new .env file', $this->command->getDescription());
        $this->assertSame('The command will generate new .env file from .env.example', $this->command->getHelp());
    }

    public function testExecShowsErrorWhenEnvExampleMissing(): void
    {
        $envExample = base_dir() . DS . '.env.example';
        $backup = $envExample . '.' . uniqid('bak', true);
        $existed = file_exists($envExample);

        if ($existed) {
            rename($envExample, $backup);
        }

        try {
            $this->tester->execute([]);

            $output = $this->tester->getDisplay();
            $this->assertStringContainsString('.env.example file not found', $output);
        } finally {
            if ($existed && file_exists($backup)) {
                rename($backup, $envExample);
            }
        }
    }
}
