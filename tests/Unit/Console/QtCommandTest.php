<?php

namespace Quantum\Tests\Unit\Console;

use Quantum\Tests\_root\shared\Commands\TestCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Helper\HelperSet;
use Quantum\Tests\Unit\AppTestCase;

class QtCommandTest extends AppTestCase
{
    private $command;

    private $tester;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new TestCommand();

        $helper = new QuestionHelper();
        $this->command->setHelperSet(new HelperSet(['question' => $helper]));

        $this->tester = new CommandTester($this->command);
    }

    public function testQtCommandMetadataIsConfigured()
    {
        $this->assertSame('test:dummy', $this->command->getName());

        $this->assertSame('Dummy test command', $this->command->getDescription());

        $this->assertSame('Used only for core command discovery tests', $this->command->getHelp());
    }

    public function testQtCommandArgumentsAndOptionsAreRegistered()
    {
        $definition = $this->command->getDefinition();

        $this->assertTrue($definition->hasArgument('uuid'));

        $this->assertTrue($definition->hasOption('force'));
    }

    public function testGetArgumentAndOption()
    {
        $this->tester->execute([
            'uuid' => '12345',
            '--force' => true,
        ]);

        $this->assertEquals('12345', $this->command->getArgument('uuid'));

        $this->assertTrue($this->command->getOption('force'));
    }

    public function testOutputMethods(): void
    {
        $this->tester->execute([]);

        $this->command->output('plain message');

        $this->command->info('info message');

        $this->command->comment('comment message');

        $this->command->error('error message');

        $display = $this->tester->getDisplay();

        $this->assertStringContainsString('plain message', $display);

        $this->assertStringContainsString('info message', $display);

        $this->assertStringContainsString('comment message', $display);

        $this->assertStringContainsString('error message', $display);
    }

    public function testQuestionOutput(): void
    {
        $this->tester->execute([]);

        $this->command->question('are you sure?');

        $display = $this->tester->getDisplay();

        $this->assertStringContainsString('are you sure?', $display);
    }

    public function testConfirmReturnsTrue(): void
    {
        $this->tester->setInputs(['y']);
        $this->tester->execute([]);

        $this->assertTrue($this->command->confirm('Confirm action'));
    }
}
