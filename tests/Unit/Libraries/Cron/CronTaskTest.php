<?php

namespace Quantum\Tests\Unit\Libraries\Cron;

use Quantum\Libraries\Cron\CronTask;
use Quantum\Libraries\Cron\Exceptions\CronException;
use PHPUnit\Framework\TestCase;

/**
 * Class CronTaskTest
 * @package Quantum\Tests\Unit\Libraries\Cron
 */
class CronTaskTest extends TestCase
{
    public function testConstructorWithValidExpression()
    {
        $task = new CronTask('test-task', '* * * * *', function () {});

        $this->assertEquals('test-task', $task->getName());
        $this->assertEquals('* * * * *', $task->getExpression());
    }

    public function testConstructorWithInvalidExpression()
    {
        $this->expectException(CronException::class);
        $this->expectExceptionMessage('Invalid cron expression');

        new CronTask('test-task', 'invalid', function () {});
    }

    public function testShouldRunEveryMinute()
    {
        $task = new CronTask('test-task', '* * * * *', function () {});

        $this->assertTrue($task->shouldRun());
    }

    public function testShouldNotRunFutureTask()
    {
        // Task scheduled for next year
        $task = new CronTask('test-task', "0 0 1 1 *", function () {});

        $this->assertFalse($task->shouldRun());
    }

    public function testHandleExecutesCallback()
    {
        $executed = false;

        $task = new CronTask('test-task', '* * * * *', function () use (&$executed) {
            $executed = true;
        });

        $task->handle();

        $this->assertTrue($executed);
    }

    public function testHandleWithCallbackArguments()
    {
        $result = null;

        $task = new CronTask('test-task', '* * * * *', function () use (&$result) {
            $result = 'executed';
        });

        $task->handle();

        $this->assertEquals('executed', $result);
    }

    public function testGetNextRunDate()
    {
        $task = new CronTask('test-task', '0 0 * * *', function () {});

        $nextRun = $task->getNextRunDate();

        $this->assertInstanceOf(\DateTime::class, $nextRun);
        $this->assertGreaterThan(new \DateTime(), $nextRun);
    }

    public function testGetPreviousRunDate()
    {
        $task = new CronTask('test-task', '0 0 * * *', function () {});

        $previousRun = $task->getPreviousRunDate();

        $this->assertInstanceOf(\DateTime::class, $previousRun);
        $this->assertLessThan(new \DateTime(), $previousRun);
    }

    public function testComplexCronExpression()
    {
        // Every 5 minutes
        $task = new CronTask('test-task', '*/5 * * * *', function () {});

        $this->assertEquals('*/5 * * * *', $task->getExpression());
    }

    public function testWeeklyCronExpression()
    {
        // Every Monday at 9 AM
        $task = new CronTask('test-task', '0 9 * * 1', function () {});

        $this->assertEquals('0 9 * * 1', $task->getExpression());
    }
}
