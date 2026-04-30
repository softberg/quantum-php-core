<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Quantum\Console\Commands\MigrationMigrateCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Helper\HelperSet;
use Quantum\Tests\Unit\AppTestCase;

class MigrationMigrateCommandTest extends AppTestCase
{
    private MigrationMigrateCommand $command;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new MigrationMigrateCommand();
        $this->command->setHelperSet(new HelperSet(['question' => new QuestionHelper()]));
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

    public function testExecCancelsDownDirectionWhenNotConfirmed(): void
    {
        $tester = new CommandTester($this->command);

        $tester->setInputs(['n']);
        $tester->execute([
            'direction' => 'down',
        ]);

        $output = $tester->getDisplay();
        $this->assertStringContainsString('Operation was canceled!', $output);
    }
}
