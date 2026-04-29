<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Quantum\Console\Commands\ModuleGenerateCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\App\App;

class ModuleGenerateCommandTest extends AppTestCase
{
    private ModuleGenerateCommand $command;
    private string $moduleName = 'Issue479Module';
    private string $modulesConfigPath;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new ModuleGenerateCommand();
        $this->modulesConfigPath = App::getBaseDir() . DS . 'shared' . DS . 'config' . DS . 'modules.php';
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

        $modulePath = App::getBaseDir() . DS . 'modules' . DS . $this->moduleName;
        $this->assertTrue($this->fs->isDirectory($modulePath));
        $this->assertStringContainsString($this->moduleName . ' module successfully created', $tester->getDisplay());
    }

    public function tearDown(): void
    {
        $moduleConfigs = $this->fs->require($this->modulesConfigPath);
        if (isset($moduleConfigs[$this->moduleName])) {
            unset($moduleConfigs[$this->moduleName]);

            $this->fs->put(
                $this->modulesConfigPath,
                "<?php\n\nreturn " . export($moduleConfigs) . ";\n"
            );
        }

        $modulePath = App::getBaseDir() . DS . 'modules' . DS . $this->moduleName;
        if ($this->fs->isDirectory($modulePath)) {
            deleteDirectoryWithFiles($modulePath);
        }

        parent::tearDown();
    }
}
