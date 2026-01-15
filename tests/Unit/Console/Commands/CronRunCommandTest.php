<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Quantum\Console\Commands\CronRunCommand;
use Symfony\Component\Console\Tester\CommandTester;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Class CronRunCommandTest
 * @package Quantum\Tests\Unit\Console\Commands
 */
class CronRunCommandTest extends TestCase
{
    private $vfsRoot;
    private $cronDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        // Create virtual filesystem
        $this->vfsRoot = vfsStream::setup('project');
        $this->cronDirectory = vfsStream::url('project/cron');
        mkdir($this->cronDirectory);

        // Setup logging config to avoid Loader dependency
        if (!config()->has('logging')) {
            config()->set('logging', [
                'default' => 'single',
                'single' => [
                    'driver' => 'single',
                    'path' => base_dir() . '/logs',
                    'level' => 'debug',
                ],
            ]);
        }
    }

    public function testCommandExecutesSuccessfully()
    {
        $this->createTaskFile('test-task.php', [
            'name' => 'test-task',
            'expression' => '* * * * *',
            'callback' => function () {},
        ]);

        $command = new CronRunCommand();
        $tester = new CommandTester($command);

        $tester->execute(['--path' => $this->cronDirectory]);

        $output = $tester->getDisplay();

        $this->assertStringContainsString('Running scheduled tasks', $output);
        $this->assertStringContainsString('Execution Summary', $output);
    }

    public function testCommandWithNoTasks()
    {
        $emptyDir = vfsStream::url('project/cron-empty');
        mkdir($emptyDir);

        $command = new CronRunCommand();
        $tester = new CommandTester($command);

        $tester->execute(['--path' => $emptyDir]);

        $output = $tester->getDisplay();

        $this->assertStringContainsString('No tasks found', $output);
    }

    public function testCommandWithSpecificTask()
    {
        $this->createTaskFile('specific-task.php', [
            'name' => 'specific-task',
            'expression' => '* * * * *',
            'callback' => function () {},
        ]);

        $command = new CronRunCommand();
        $tester = new CommandTester($command);

        $tester->execute([
            '--path' => $this->cronDirectory,
            '--task' => 'specific-task',
        ]);

        $output = $tester->getDisplay();

        $this->assertStringContainsString('Running task: specific-task', $output);
    }

    public function testCommandWithForceOption()
    {
        $this->createTaskFile('force-task.php', [
            'name' => 'force-task',
            'expression' => '* * * * *',
            'callback' => function () {},
        ]);

        $command = new CronRunCommand();
        $tester = new CommandTester($command);

        $tester->execute([
            '--path' => $this->cronDirectory,
            '--force' => true,
        ]);

        $this->assertEquals(0, $tester->getStatusCode());
    }

    public function testCommandWithNonExistentTask()
    {
        $command = new CronRunCommand();
        $tester = new CommandTester($command);

        $tester->execute([
            '--path' => $this->cronDirectory,
            '--task' => 'non-existent',
        ]);

        $output = $tester->getDisplay();

        $this->assertStringContainsString('not found', $output);
    }

    public function testCommandDisplaysStatistics()
    {
        $this->createTaskFile('task1.php', [
            'name' => 'task-1',
            'expression' => '* * * * *',
            'callback' => function () {},
        ]);

        $this->createTaskFile('task2.php', [
            'name' => 'task-2',
            'expression' => '0 0 1 1 *',
            'callback' => function () {},
        ]);

        $command = new CronRunCommand();
        $tester = new CommandTester($command);

        $tester->execute(['--path' => $this->cronDirectory]);

        $output = $tester->getDisplay();

        $this->assertStringContainsString('Total tasks:', $output);
        $this->assertStringContainsString('Executed:', $output);
        $this->assertStringContainsString('Skipped:', $output);
    }

    public function testCommandHandlesTaskFailure()
    {
        $this->createTaskFile('failing-task.php', [
            'name' => 'failing-task',
            'expression' => '* * * * *',
            'body' => "throw new \\Exception('Task failed');",
        ]);

        $command = new CronRunCommand();
        $tester = new CommandTester($command);

        $tester->execute(['--path' => $this->cronDirectory]);

        $output = $tester->getDisplay();

        $this->assertStringContainsString('Failed:', $output);
    }

    public function testCommandShortOptions()
    {
        $this->createTaskFile('short-option-task.php', [
            'name' => 'short-option-task',
            'expression' => '* * * * *',
            'callback' => function () {},
        ]);

        $command = new CronRunCommand();
        $tester = new CommandTester($command);

        $tester->execute([
            '--path' => $this->cronDirectory,
            '-t' => 'short-option-task',
        ]);

        $output = $tester->getDisplay();

        $this->assertStringContainsString('short-option-task', $output);
    }

    private function createTaskFile(string $filename, array $definition): void
    {
        $body = $definition['body'] ?? "echo 'Test task executed';";

        $content = "<?php\n\nreturn [\n";
        $content .= "    'name' => '{$definition['name']}',\n";
        $content .= "    'expression' => '{$definition['expression']}',\n";
        $content .= "    'callback' => function () {\n";
        $content .= "        {$body}\n";
        $content .= "    }\n";
        $content .= "];\n";

        file_put_contents($this->cronDirectory . '/' . $filename, $content);
    }
}
