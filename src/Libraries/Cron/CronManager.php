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

namespace Quantum\Libraries\Cron;

use Quantum\Libraries\Cron\Contracts\CronTaskInterface;
use Quantum\Libraries\Logger\Factories\LoggerFactory;
use Quantum\Libraries\Cron\Exceptions\CronException;

/**
 * Class CronManager
 * @package Quantum\Libraries\Cron
 */
class CronManager
{
    /**
     * Loaded tasks
     * @var array<string, CronTaskInterface>
     */
    private $tasks = [];

    /**
     * Cron directory path
     * @var string
     */
    private $cronDirectory;

    /**
     * Execution statistics
     * @var array
     */
    private $stats = [
        'total' => 0,
        'executed' => 0,
        'skipped' => 0,
        'failed' => 0,
        'locked' => 0,
    ];

    /**
     * CronManager constructor
     * @param string|null $cronDirectory
     */
    public function __construct(?string $cronDirectory = null)
    {
        $configuredPath = $cronDirectory ?? cron_config('path');
        $this->cronDirectory = $configuredPath ?: $this->getDefaultCronDirectory();
    }

    /**
     * Load tasks from cron directory
     * @return void
     * @throws CronException
     */
    public function loadTasks(): void
    {
        if (!fs()->isDirectory($this->cronDirectory)) {
            if ($this->cronDirectory !== $this->getDefaultCronDirectory()) {
                throw CronException::cronDirectoryNotFound($this->cronDirectory);
            }
            return;
        }

        $files = fs()->glob($this->cronDirectory . DS . '*.php') ?: [];

        foreach ($files as $file) {
            $this->loadTaskFromFile($file);
        }

        $this->stats['total'] = count($this->tasks);
    }

    /**
     * Load a single task from file
     * @param string $file
     * @return void
     * @throws CronException
     */
    private function loadTaskFromFile(string $file): void
    {
        $task = fs()->require($file);

        if (is_array($task)) {
            $task = $this->createTaskFromArray($task);
        }

        if (!$task instanceof CronTaskInterface) {
            throw CronException::invalidTaskFile($file);
        }

        $this->tasks[$task->getName()] = $task;
    }

    /**
     * Create task from array definition
     * @param array $definition
     * @return CronTask
     * @throws CronException
     */
    private function createTaskFromArray(array $definition): CronTask
    {
        if (!isset($definition['name'], $definition['expression'], $definition['callback'])) {
            throw new CronException('Task definition must contain name, expression, and callback');
        }

        return new CronTask(
            $definition['name'],
            $definition['expression'],
            $definition['callback']
        );
    }

    /**
     * Run all due tasks
     * @param bool $force Ignore locks
     * @return array Statistics
     */
    public function runDueTasks(bool $force = false): array
    {
        $this->loadTasks();

        foreach ($this->tasks as $task) {
            if ($task->shouldRun()) {
                $this->runTask($task, $force);
            } else {
                $this->stats['skipped']++;
            }
        }

        return $this->stats;
    }

    /**
     * Run a specific task by name
     * @param string $taskName
     * @param bool $force Ignore locks
     * @return void
     * @throws CronException
     */
    public function runTaskByName(string $taskName, bool $force = false): void
    {
        $this->loadTasks();

        if (!isset($this->tasks[$taskName])) {
            throw CronException::taskNotFound($taskName);
        }

        $this->runTask($this->tasks[$taskName], $force);
    }

    /**
     * Run a single task
     * @param CronTaskInterface $task
     * @param bool $force Ignore locks
     * @return void
     */
    private function runTask(CronTaskInterface $task, bool $force = false): void
    {
        $lock = new CronLock($task->getName());

        if (!$force && !$lock->acquire()) {
            $this->stats['locked']++;
            $this->log('warning', "Task \"{$task->getName()}\" skipped: locked");
            return;
        }

        $startTime = microtime(true);
        $this->log('info', "Task \"{$task->getName()}\" started");

        try {
            $task->handle();
            $duration = round(microtime(true) - $startTime, 2);
            $this->stats['executed']++;
            $this->log('info', "Task \"{$task->getName()}\" completed in {$duration}s");
        } catch (\Throwable $e) {
            $this->stats['failed']++;
            $this->log('error', "Task \"{$task->getName()}\" failed: " . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        } finally {
            if (!$force) {
                $lock->release();
            }
        }
    }

    /**
     * Get all loaded tasks
     * @return array<string, CronTaskInterface>
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }

    /**
     * Get execution statistics
     * @return array
     */
    public function getStats(): array
    {
        return $this->stats;
    }

    /**
     * Get default cron directory
     * @return string
     */
    private function getDefaultCronDirectory(): string
    {
        return base_dir() . DS . 'cron';
    }

    /**
     * Log a message
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    private function log(string $level, string $message, array $context = []): void
    {
        try {
            $logger = LoggerFactory::get();
            $logger->log($level, '[CRON] ' . $message, $context);
        } catch (\Throwable $exception) {
            error_log(sprintf('[CRON] [%s] %s', strtoupper($level), $message));
        }
    }
}
