<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Console\Commands;

use Quantum\Libraries\Cron\CronManager;
use Quantum\Libraries\Cron\Exceptions\CronException;
use Quantum\Console\QtCommand;

/**
 * Class CronRunCommand
 * @package Quantum\Console
 */
class CronRunCommand extends QtCommand
{
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'cron:run';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Run scheduled cron tasks';

    /**
     * Command help text.
     * @var string
     */
    protected $help = 'Executes scheduled tasks defined in the cron directory. Use --task to run a single task or --force to bypass locks.';

    /**
     * Command options
     * @var array<int, list<string|null>>
     */
    protected $options = [
        ['force', 'f', 'none', 'Force run tasks ignoring locks'],
        ['task', 't', 'optional', 'Run a specific task by name'],
        ['path', 'p', 'optional', 'Custom cron directory path'],
    ];

    /**
     * Executes the command
     */
    public function exec()
    {
        $force = (bool) $this->getOption('force');
        $taskName = $this->getOption('task');
        $cronPath = $this->getOption('path') ?: getenv('QT_CRON_PATH') ?: null;

        try {
            $manager = new CronManager($cronPath);

            if ($taskName) {
                $this->runSpecificTask($manager, $taskName, $force);
            } else {
                $this->runAllDueTasks($manager, $force);
            }
        } catch (CronException $e) {
            $this->error($e->getMessage());
        } catch (\Throwable $e) {
            $this->error('Unexpected error: ' . $e->getMessage());
        }
    }

    /**
     * Run all due tasks
     * @param CronManager $manager
     * @param bool $force
     * @return void
     */
    private function runAllDueTasks(CronManager $manager, bool $force): void
    {
        $this->info('Running scheduled tasks...');

        $stats = $manager->runDueTasks($force);

        $this->output('');
        $this->info('Execution Summary:');
        $this->output("  Total tasks: {$stats['total']}");
        $this->output("  Executed: <info>{$stats['executed']}</info>");
        $this->output("  Skipped: {$stats['skipped']}");

        if ($stats['locked'] > 0) {
            $this->output("  Locked: <comment>{$stats['locked']}</comment>");
        }

        if ($stats['failed'] > 0) {
            $this->output("  Failed: <error>{$stats['failed']}</error>");
        }

        $this->output('');

        if ($stats['executed'] > 0) {
            $this->info('✓ Tasks completed successfully');
        } elseif ($stats['total'] === 0) {
            $this->comment('No tasks found in cron directory');
        } else {
            $this->comment('No tasks were due to run');
        }
    }

    /**
     * Run a specific task
     * @param CronManager $manager
     * @param string $taskName
     * @param bool $force
     * @return void
     * @throws CronException
     */
    private function runSpecificTask(CronManager $manager, string $taskName, bool $force): void
    {
        $this->info("Running task: {$taskName}");

        $manager->runTaskByName($taskName, $force);

        $stats = $manager->getStats();

        if ($stats['executed'] > 0) {
            $this->info("✓ Task '{$taskName}' completed successfully");
        } elseif ($stats['failed'] > 0) {
            $this->error("✗ Task '{$taskName}' failed");
        } elseif ($stats['locked'] > 0) {
            $this->comment("⚠ Task '{$taskName}' is locked");
        }
    }
}
