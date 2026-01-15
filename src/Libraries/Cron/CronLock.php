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

use Quantum\Libraries\Cron\Exceptions\CronException;

/**
 * Class CronLock
 * @package Quantum\Libraries\Cron
 */
class CronLock
{
    /**
     * Lock directory path
     * @var string
     */
    private $lockDirectory;

    /**
     * Task name
     * @var string
     */
    private $taskName;

    /**
     * Lock file path
     * @var string|null
     */
    private $lockFile = null;

    /**
     * Lock file handle
     * @var resource|null
     */
    private $lockHandle = null;

    /**
     * Maximum lock age in seconds (24 hours)
     */
    private const MAX_LOCK_AGE = 86400;

    /**
     * CronLock constructor
     * @param string $taskName
     * @param string|null $lockDirectory
     * @throws CronException
     */
    public function __construct(string $taskName, ?string $lockDirectory = null)
    {
        $this->taskName = $taskName;
        $this->lockDirectory = $lockDirectory ?? $this->getDefaultLockDirectory();
        $this->lockFile = $this->lockDirectory . DIRECTORY_SEPARATOR . $this->taskName . '.lock';

        $this->ensureLockDirectoryExists();
        $this->cleanupStaleLocks();
    }

    /**
     * Acquire lock for the task
     * @return bool
     */
    public function acquire(): bool
    {
        if ($this->isLocked()) {
            return false;
        }

        $this->lockHandle = fopen($this->lockFile, 'w');

        if ($this->lockHandle === false) {
            return false;
        }

        if (!flock($this->lockHandle, LOCK_EX | LOCK_NB)) {
            fclose($this->lockHandle);
            $this->lockHandle = null;
            return false;
        }

        fwrite($this->lockHandle, json_encode([
            'task' => $this->taskName,
            'started_at' => time(),
            'pid' => getmypid(),
        ]));

        fflush($this->lockHandle);

        return true;
    }

    /**
     * Release the lock
     * @return bool
     */
    public function release(): bool
    {
        if ($this->lockHandle !== null) {
            flock($this->lockHandle, LOCK_UN);
            fclose($this->lockHandle);
            $this->lockHandle = null;
        }

        if (file_exists($this->lockFile)) {
            return unlink($this->lockFile);
        }

        return true;
    }

    /**
     * Check if task is locked
     * @return bool
     */
    public function isLocked(): bool
    {
        if (!file_exists($this->lockFile)) {
            return false;
        }

        // Check if lock is stale
        if (time() - filemtime($this->lockFile) > self::MAX_LOCK_AGE) {
            unlink($this->lockFile);
            return false;
        }

        // Try to open the file to check if it's actually locked
        $handle = @fopen($this->lockFile, 'r');
        if ($handle === false) {
            return true;
        }

        $locked = !flock($handle, LOCK_EX | LOCK_NB);

        if (!$locked) {
            flock($handle, LOCK_UN);
        }

        fclose($handle);

        return $locked;
    }

    /**
     * Get default lock directory
     * @return string
     */
    private function getDefaultLockDirectory(): string
    {
        $baseDir = base_dir() . DIRECTORY_SEPARATOR . 'runtime';
        return $baseDir . DIRECTORY_SEPARATOR . 'cron' . DIRECTORY_SEPARATOR . 'locks';
    }

    /**
     * Ensure lock directory exists
     * @throws CronException
     */
    private function ensureLockDirectoryExists(): void
    {
        if (!is_dir($this->lockDirectory)) {
            if (!mkdir($this->lockDirectory, 0755, true)) {
                throw CronException::lockDirectoryNotWritable($this->lockDirectory);
            }
        }

        if (!is_writable($this->lockDirectory)) {
            throw CronException::lockDirectoryNotWritable($this->lockDirectory);
        }
    }

    /**
     * Cleanup stale locks
     */
    private function cleanupStaleLocks(): void
    {
        if (!is_dir($this->lockDirectory)) {
            return;
        }

        $files = glob($this->lockDirectory . DIRECTORY_SEPARATOR . '*.lock');

        foreach ($files as $file) {
            if (time() - filemtime($file) > self::MAX_LOCK_AGE) {
                @unlink($file);
            }
        }
    }

    /**
     * Destructor - ensure lock is released
     */
    public function __destruct()
    {
        $this->release();
    }
}
