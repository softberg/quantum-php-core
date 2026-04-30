<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Quantum\Console\Commands\MigrationGenerateCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Quantum\Tests\Unit\AppTestCase;

class MigrationGenerateCommandTest extends AppTestCase
{
    private MigrationGenerateCommand $command;
    /** @var array<int, string> */
    private array $existingMigrationFiles = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new MigrationGenerateCommand();

        $migrationsPath = base_dir() . DS . 'migrations';
        if (!is_dir($migrationsPath)) {
            mkdir($migrationsPath, 0777, true);
        }

        $files = glob($migrationsPath . DS . '*.php');
        $this->existingMigrationFiles = is_array($files) ? $files : [];
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

    public function testExecCreatesMigrationFile(): void
    {
        $tester = new CommandTester($this->command);

        $tester->execute([
            'action' => 'create',
            'table' => 'tickets',
        ]);

        $output = $tester->getDisplay();
        $this->assertStringContainsString('Migration file', $output);
        $this->assertStringContainsString('successfully created', $output);
    }

    public function tearDown(): void
    {
        $migrationsPath = base_dir() . DS . 'migrations';
        $files = glob($migrationsPath . DS . '*.php');
        $currentFiles = is_array($files) ? $files : [];

        $createdByTest = array_diff($currentFiles, $this->existingMigrationFiles);

        foreach ($createdByTest as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        parent::tearDown();
    }
}
