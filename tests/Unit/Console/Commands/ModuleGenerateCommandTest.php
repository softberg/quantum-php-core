<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Quantum\Console\Commands\ModuleGenerateCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\App\App;

class ModuleGenerateCommandTest extends AppTestCase
{
    private ModuleGenerateCommand $command;
    private string $moduleName;
    private string $modulesConfigPath;
    private string $modulePath;
    private bool $moduleCreatedByTest = false;
    private bool $moduleConfigCreatedByTest = false;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new ModuleGenerateCommand();
        $this->moduleName = str_replace('.', '_', uniqid('Issue479Module_', true));
        $this->modulesConfigPath = App::getBaseDir() . DS . 'shared' . DS . 'config' . DS . 'modules.php';
        $this->modulePath = App::getBaseDir() . DS . 'modules' . DS . $this->moduleName;
    }

    public function testCommandMetadata(): void
    {
        $this->assertSame('module:generate', $this->command->getName());
        $this->assertSame('Generate new module', $this->command->getDescription());
        $this->assertSame('The command will create files for new module', $this->command->getHelp());
    }

    public function testCommandArgumentsAndOptionsAreRegistered(): void
    {
        $definition = $this->command->getDefinition();

        $this->assertTrue($definition->hasArgument('module'));
        $this->assertTrue($definition->getArgument('module')->isRequired());

        $this->assertTrue($definition->hasOption('yes'));
        $this->assertTrue($definition->hasOption('template'));
        $this->assertTrue($definition->hasOption('with-assets'));
    }

    public function testExecCreatesModule(): void
    {
        $tester = new CommandTester($this->command);

        $tester->execute([
            'module' => $this->moduleName,
            '--yes' => true,
            '--template' => 'DefaultApi',
            '--with-assets' => false,
        ]);

        $this->assertTrue($this->fs->isDirectory($this->modulePath));
        $this->moduleCreatedByTest = true;

        $moduleConfigs = $this->fs->require($this->modulesConfigPath);
        if (is_array($moduleConfigs) && isset($moduleConfigs[$this->moduleName])) {
            $this->moduleConfigCreatedByTest = true;
        }

        $this->assertStringContainsString($this->moduleName . ' module successfully created', $tester->getDisplay());
    }

    public function tearDown(): void
    {
        if ($this->fs->exists($this->modulesConfigPath)) {
            $moduleConfigs = $this->fs->require($this->modulesConfigPath);
            if (
                $this->moduleConfigCreatedByTest
                && is_array($moduleConfigs)
                && isset($moduleConfigs[$this->moduleName])
            ) {
                unset($moduleConfigs[$this->moduleName]);

                $this->fs->put(
                    $this->modulesConfigPath,
                    "<?php\n\nreturn " . export($moduleConfigs) . ";\n"
                );
            }
        }

        if (
            $this->moduleCreatedByTest
            && $this->fs->isDirectory($this->modulePath)
            && basename($this->modulePath) === $this->moduleName
            && strpos($this->moduleName, 'Issue479Module_') === 0
        ) {
            deleteDirectoryWithFiles($this->modulePath);
        }

        parent::tearDown();
    }
}
