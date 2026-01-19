<?php

namespace Quantum\Tests\Unit\Console\Commands;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Console\Commands\CronRunCommand;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class CronRunCommandTest
 * @package Quantum\Tests\Unit\Console\Commands
 */
class CronRunCommandTest extends AppTestCase
{
    private $cronDirectory;

    public function setUp(): void
    {
        parent::setUp();

        $this->cronDirectory = base_dir() . DS . 'cron-command-tests';
        $this->cleanupDirectory($this->cronDirectory);
        mkdir($this->cronDirectory, 0777, true);

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
        config()->set('cron', [
            'path' => null,
            'lock_path' => null,
            'max_lock_age' => 86400,
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->cleanupDirectory($this->cronDirectory);
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
        $emptyDir = $this->cronDirectory . '-empty';
        $this->cleanupDirectory($emptyDir);
        mkdir($emptyDir, 0777, true);

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

        $this->assertStringContainsString('Failed: 1', $output);
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

    public function testCommandUsesConfiguredPath()
    {
        $this->createTaskFile('config-task.php', [
            'name' => 'config-task',
            'expression' => '* * * * *',
            'callback' => function () {},
        ]);

        config()->set('cron', [
            'path' => $this->cronDirectory,
            'lock_path' => null,
            'max_lock_age' => 86400,
        ]);

        $command = new CronRunCommand();
        $tester = new CommandTester($command);

        $tester->execute([]);

        $output = $tester->getDisplay();

        $this->assertStringContainsString('Execution Summary', $output);
    }

    public function testCommandReportsLockedTasks()
    {
        $this->createTaskFile('locked-task.php', [
            'name' => 'locked-task',
            'expression' => '* * * * *',
        ]);

        $lock = new \Quantum\Libraries\Cron\CronLock('locked-task', $this->runtimeDirectory . DS . 'locks');
        $lock->acquire(); // Hold the lock

        $command = new CronRunCommand();
        $tester = new CommandTester($command);

        $tester->execute(['--path' => $this->cronDirectory]);

        $output = $tester->getDisplay();
        $this->assertStringContainsString('Locked: 1', $output);

        $lock->release();
    }

    public function testCommandHandlesUnexpectedError()
    {
        $command = new CronRunCommand();
        $tester = new CommandTester($command);

        $tester->execute(['--path' => []]);

        $output = $tester->getDisplay();
        $this->assertStringContainsString('Unexpected error', $output);
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

        file_put_contents($this->cronDirectory . DS . $filename, $content);
    }

    private function cleanupDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $items = scandir($directory);

        foreach ($items as $item) {
            if (in_array($item, ['.', '..'], true)) {
                continue;
            }

            $path = $directory . DS . $item;

            if (is_dir($path)) {
                $this->cleanupDirectory($path);
            } elseif (file_exists($path)) {
                @unlink($path);
            }
        }

        @rmdir($directory);
    }
}
