<?php

namespace Quantum\Tests\Unit\Cron;

use Quantum\Cron\Exceptions\CronException;
use PHPUnit\Framework\TestCase;

class CronExceptionTest extends TestCase
{
    public function testTaskNotFound(): void
    {
        $exception = CronException::taskNotFound('my-task');
        $this->assertEquals('Cron task "my-task" not found', $exception->getMessage());
    }

    public function testInvalidExpression(): void
    {
        $exception = CronException::invalidExpression('invalid-expr');
        $this->assertEquals('Invalid cron expression: invalid-expr', $exception->getMessage());
    }

    public function testLockAcquireFailed(): void
    {
        $exception = CronException::lockAcquireFailed('my-task');
        $this->assertEquals('Failed to acquire lock for task "my-task"', $exception->getMessage());
    }

    public function testTaskExecutionFailed(): void
    {
        $exception = CronException::taskExecutionFailed('my-task', 'Connection timeout');
        $this->assertEquals('Task "my-task" execution failed: Connection timeout', $exception->getMessage());
    }

    public function testInvalidTaskFile(): void
    {
        $exception = CronException::invalidTaskFile('invalid-file.php');
        $this->assertEquals('Invalid task file "invalid-file.php": must return array or CronTask instance', $exception->getMessage());
    }

    public function testCronDirectoryNotFound(): void
    {
        $exception = CronException::cronDirectoryNotFound('/path/to/cron');
        $this->assertEquals('Cron directory not found: /path/to/cron', $exception->getMessage());
    }

    public function testLockDirectoryNotWritable(): void
    {
        $exception = CronException::lockDirectoryNotWritable('/path/to/lock');
        $this->assertEquals('Lock directory is not writable: /path/to/lock', $exception->getMessage());
    }
}
