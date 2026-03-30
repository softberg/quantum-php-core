<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Helper\HelperSet;
use Quantum\Console\Commands\KeyGenerateCommand;
use Quantum\Tests\Unit\AppTestCase;

class KeyGenerateCommandTest extends AppTestCase
{
    private KeyGenerateCommand $command;

    private CommandTester $tester;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new KeyGenerateCommand();
        $this->command->setHelperSet(new HelperSet(['question' => new QuestionHelper()]));
        $this->tester = new CommandTester($this->command);
    }

    public function testCommandMetadata(): void
    {
        $this->assertSame('core:key', $this->command->getName());
        $this->assertSame('Generates and stores the application key', $this->command->getDescription());
        $this->assertSame('The command will generate APP_KEY and store in .env file', $this->command->getHelp());
    }

    public function testCommandOptionsAreRegistered(): void
    {
        $definition = $this->command->getDefinition();

        $this->assertTrue($definition->hasOption('length'));
        $this->assertTrue($definition->hasOption('yes'));
    }

    public function testExecGeneratesKey(): void
    {
        $this->tester->execute(['--yes' => true, '--length' => 16]);

        $output = $this->tester->getDisplay();
        $this->assertStringContainsString('Application key successfully generated', $output);
        $this->assertNotEmpty(env('APP_KEY'));
    }

    public function testExecCancelsWhenNotConfirmed(): void
    {
        env('APP_KEY', 'existing-key');

        $this->tester->setInputs(['n']);
        $this->tester->execute([]);

        $output = $this->tester->getDisplay();
        $this->assertStringContainsString('Operation was canceled', $output);
    }
}
