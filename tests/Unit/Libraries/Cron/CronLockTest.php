<?php

namespace Quantum\Tests\Unit\Libraries\Cron;

use Quantum\Libraries\Cron\CronLock;
use Quantum\Libraries\Cron\Exceptions\CronException;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Class CronLockTest
 * @package Quantum\Tests\Unit\Libraries\Cron
 */
class CronLockTest extends TestCase
{
    private $vfsRoot;
    private $lockDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        // Create virtual filesystem
        $this->vfsRoot = vfsStream::setup('runtime');
        $this->lockDirectory = vfsStream::url('runtime/cron/locks');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Cleanup any real lock files if they exist
        $realLockDir = base_dir() . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'cron' . DIRECTORY_SEPARATOR . 'locks';
        if (is_dir($realLockDir)) {
            $files = glob($realLockDir . '/*.lock');
            foreach ($files as $file) {
                @unlink($file);
            }
        }
    }

    public function testConstructorCreatesLockDirectory()
    {
        $lock = new CronLock('test-task', $this->lockDirectory);

        $this->assertTrue(is_dir($this->lockDirectory));
    }

    public function testAcquireLock()
    {
        $lock = new CronLock('test-task', $this->lockDirectory);

        $this->assertTrue($lock->acquire());
    }

    public function testCannotAcquireLockedTask()
    {
        $lock1 = new CronLock('test-task', $this->lockDirectory);
        $lock1->acquire();

        $lock2 = new CronLock('test-task', $this->lockDirectory);

        $this->assertFalse($lock2->acquire());

        $lock1->release();
    }

    public function testReleaseLock()
    {
        $lock = new CronLock('test-task', $this->lockDirectory);
        $lock->acquire();

        $this->assertTrue($lock->release());
        $this->assertFalse($lock->isLocked());
    }

    public function testIsLocked()
    {
        $lock1 = new CronLock('test-task', $this->lockDirectory);

        $this->assertFalse($lock1->isLocked());

        $lock1->acquire();

        $lock2 = new CronLock('test-task', $this->lockDirectory);
        $this->assertTrue($lock2->isLocked());

        $lock1->release();

        $this->assertFalse($lock2->isLocked());
    }

    public function testMultipleTasksCanHaveSeparateLocks()
    {
        $lock1 = new CronLock('task-1', $this->lockDirectory);
        $lock2 = new CronLock('task-2', $this->lockDirectory);

        $this->assertTrue($lock1->acquire());
        $this->assertTrue($lock2->acquire());

        $lock1->release();
        $lock2->release();
    }

    public function testLockFileContainsMetadata()
    {
        $lock = new CronLock('test-task', $this->lockDirectory);
        $lock->acquire();

        $lockFile = $this->lockDirectory . '/test-task.lock';
        $this->assertTrue(file_exists($lockFile));

        $content = file_get_contents($lockFile);
        $data = json_decode($content, true);

        $this->assertArrayHasKey('task', $data);
        $this->assertArrayHasKey('started_at', $data);
        $this->assertArrayHasKey('pid', $data);
        $this->assertEquals('test-task', $data['task']);

        $lock->release();
    }

    public function testDestructorReleasesLock()
    {
        $lockFile = $this->lockDirectory . '/test-task.lock';

        $lock = new CronLock('test-task', $this->lockDirectory);
        $lock->acquire();

        $this->assertTrue(file_exists($lockFile));

        unset($lock);

        // Lock should be released after destructor
        $newLock = new CronLock('test-task', $this->lockDirectory);
        $this->assertFalse($newLock->isLocked());
    }

    public function testThrowsExceptionWhenDirectoryNotWritable()
    {
        $this->expectException(CronException::class);
        $this->expectExceptionMessage('not writable');

        // Create a read-only directory
        $readOnlyDir = vfsStream::url('runtime/readonly');
        mkdir($readOnlyDir, 0444);

        new CronLock('test-task', $readOnlyDir);
    }
}
