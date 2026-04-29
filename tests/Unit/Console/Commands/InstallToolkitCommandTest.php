<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Quantum\Console\Commands\InstallToolkitCommand;
use Symfony\Component\Console\Tester\CommandTester;
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

    public function testExecUpdatesEnvAndRunsModuleGeneration(): void
    {
        $command = new class extends InstallToolkitCommand {
            public string $calledCommand = '';

            /** @var array<string, mixed> */
            public array $calledArguments = [];

            protected function runExternalCommand(string $commandName, array $arguments): void
            {
                $this->calledCommand = $commandName;
                $this->calledArguments = $arguments;
            }
        };

        $tester = new CommandTester($command);
        $tester->execute([
            'username' => 'test-user',
            'password' => 'test-pass',
        ]);

        $this->assertSame('test-user', env('BASIC_AUTH_NAME'));
        $this->assertSame('test-pass', env('BASIC_AUTH_PWD'));
        $this->assertSame(InstallToolkitCommand::COMMAND_CREATE_MODULE, $command->calledCommand);
        $this->assertSame([
            'module' => 'Toolkit',
            '--yes' => true,
            '--template' => 'Toolkit',
            '--with-assets' => true,
        ], $command->calledArguments);
        $this->assertStringContainsString('Toolkit installed successfully', $tester->getDisplay());
    }
}
