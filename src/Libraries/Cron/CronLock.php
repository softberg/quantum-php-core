<?php

namespace Quantum\Libraries\Cron;

use Quantum\Libraries\Cron\Exceptions\CronException;

/**
 * CronLock - file based lock with flock()
 *
 * Changes vs original:
 * - taskName sanitized for safe filename
 * - release() removes lock file
 * - stale cleanup reads timestamp from locked handle (no extra fs()->get)
 * - isLocked() is non-destructive (does NOT delete files); cleanup handles stale deletion
 * - optional refresh() to update timestamp while running
 */
class CronLock
{
    private string $lockDirectory;
    private string $taskName;
    private string $lockFile;

    /** @var resource|null */
    private $lockHandle = null;

    private bool $ownsLock = false;
    private int $maxLockAge;

    private const DEFAULT_MAX_LOCK_AGE = 86400;

    public function __construct(string $taskName, ?string $lockDirectory = null, ?int $maxLockAge = null)
    {
        $this->taskName = $this->sanitizeTaskName($taskName);
        $this->lockDirectory = $this->resolveLockDirectory($lockDirectory);
        $this->lockFile = $this->lockDirectory . DS . $this->taskName . '.lock';
        $this->maxLockAge = $maxLockAge ?? (int) cron_config('max_lock_age', self::DEFAULT_MAX_LOCK_AGE);

        $this->ensureLockDirectoryExists();
        $this->cleanupStaleLocks();
    }

    public function acquire(): bool
    {
        $this->lockHandle = fopen($this->lockFile, 'c+');
        if ($this->lockHandle === false) {
            $this->lockHandle = null;
            $this->ownsLock = false;
            return false;
        }

        if (!flock($this->lockHandle, LOCK_EX | LOCK_NB)) {
            fclose($this->lockHandle);
            $this->lockHandle = null;
            $this->ownsLock = false;
            return false;
        }

        if (!$this->writeTimestampToHandle($this->lockHandle)) {
            flock($this->lockHandle, LOCK_UN);
            fclose($this->lockHandle);
            $this->lockHandle = null;
            $this->ownsLock = false;
            return false;
        }
        $this->ownsLock = true;

        return true;
    }

    /**
     * Update lock timestamp (useful for long-running jobs)
     */
    public function refresh(): bool
    {
        if (!$this->ownsLock || $this->lockHandle === null) {
            return false;
        }

        return $this->writeTimestampToHandle($this->lockHandle);
    }

    public function release(): bool
    {
        if (!$this->ownsLock || $this->lockHandle === null) {
            return true;
        }

        $unlocked = flock($this->lockHandle, LOCK_UN);
        $closed = fclose($this->lockHandle);

        $this->lockHandle = null;
        $this->ownsLock = false;

        $removed = true;
        if (fs()->exists($this->lockFile)) {
            $removed = fs()->remove($this->lockFile);
        }

        return $unlocked && $closed && $removed;
    }

    /**
     * Check if another process currently holds the lock.
     */
    public function isLocked(): bool
    {
        if (!fs()->exists($this->lockFile)) {
            return false;
        }

        $handle = fopen($this->lockFile, 'c+');
        if ($handle === false) {
            return true;
        }

        if (!flock($handle, LOCK_EX | LOCK_NB)) {
            fclose($handle);
            return true;
        }

        flock($handle, LOCK_UN);
        fclose($handle);

        return false;
    }

    private function sanitizeTaskName(string $taskName): string
    {
        $taskName = trim($taskName);
        if ($taskName === '') {
            return 'default';
        }

        // Keep safe filename chars only
        $taskName = preg_replace('/[^a-zA-Z0-9._-]+/', '_', $taskName) ?? 'default';
        $taskName = trim($taskName, '._-');

        return $taskName !== '' ? $taskName : 'default';
    }

    private function resolveLockDirectory(?string $lockDirectory): string
    {
        $path = $lockDirectory ?? cron_config('lock_path');
        return $path === null ? $this->getDefaultLockDirectory() : $path;
    }

    private function getDefaultLockDirectory(): string
    {
        return base_dir() . DS . 'runtime' . DS . 'cron' . DS . 'locks';
    }

    private function ensureLockDirectoryExists(): void
    {
        if ($this->lockDirectory === '') {
            throw CronException::lockDirectoryNotWritable('');
        }

        $this->createDirectory($this->lockDirectory);

        if (!fs()->isWritable($this->lockDirectory)) {
            throw CronException::lockDirectoryNotWritable($this->lockDirectory);
        }
    }

    private function createDirectory(string $directory): void
    {
        if (fs()->isDirectory($directory)) {
            return;
        }

        $parent = dirname($directory);
        if ($parent && $parent !== $directory) {
            $this->createDirectory($parent);
        }

        // @phpstan-ignore-next-line
        if (!fs()->makeDirectory($directory) && !fs()->isDirectory($directory)) {
            throw CronException::lockDirectoryNotWritable($directory);
        }
    }

    /**
     * Removes stale lock files that are NOT currently locked by any process.
     * Safe because we take LOCK_EX before removing.
     */
    private function cleanupStaleLocks(): void
    {
        if (!fs()->isDirectory($this->lockDirectory)) {
            return;
        }

        $files = fs()->glob($this->lockDirectory . DS . '*.lock') ?: [];
        $now = time();

        foreach ($files as $file) {
            $handle = fopen($file, 'c+');
            if ($handle === false) {
                continue;
            }

            // If someone holds it, skip
            if (!flock($handle, LOCK_EX | LOCK_NB)) {
                fclose($handle);
                continue;
            }

            $timestamp = $this->readTimestampFromHandle($handle);

            if ($timestamp !== null && ($now - $timestamp) > $this->maxLockAge) {
                flock($handle, LOCK_UN);
                fclose($handle);
                fs()->remove($file);
                continue;
            }

            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    private function writeTimestampToHandle($handle): bool
    {
        if (ftruncate($handle, 0) === false) {
            return false;
        }
        if (rewind($handle) === false) {
            return false;
        }
        if (fwrite($handle, (string) time()) === false) {
            return false;
        }
        if (fflush($handle) === false) {
            return false;
        }

        return true;
    }

    private function readTimestampFromHandle($handle): ?int
    {
        if (rewind($handle) === false) {
            return null;
        }
        $content = stream_get_contents($handle);
        if ($content === false) {
            return null;
        }

        $timestamp = (int) trim((string) $content);
        return $timestamp > 0 ? $timestamp : null;
    }
}
