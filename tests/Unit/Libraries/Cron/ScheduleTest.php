<?php

namespace Quantum\Tests\Unit\Libraries\Cron;

use Quantum\Libraries\Cron\Schedule;
use Quantum\Libraries\Cron\CronTask;
use Quantum\Libraries\Cron\Exceptions\CronException;
use PHPUnit\Framework\TestCase;

class ScheduleTest extends TestCase
{
    private $schedule;

    public function setUp(): void
    {
        $this->schedule = new Schedule('test-task');
    }

    public function testEveryMinute()
    {
        $this->schedule->everyMinute();
        $this->assertEquals('* * * * *', $this->schedule->getExpression());
    }

    public function testEveryFiveMinutes()
    {
        $this->schedule->everyFiveMinutes();
        $this->assertEquals('*/5 * * * *', $this->schedule->getExpression());
    }

    public function testEveryTenMinutes()
    {
        $this->schedule->everyTenMinutes();
        $this->assertEquals('*/10 * * * *', $this->schedule->getExpression());
    }

    public function testEveryFifteenMinutes()
    {
        $this->schedule->everyFifteenMinutes();
        $this->assertEquals('*/15 * * * *', $this->schedule->getExpression());
    }

    public function testEveryThirtyMinutes()
    {
        $this->schedule->everyThirtyMinutes();
        $this->assertEquals('*/30 * * * *', $this->schedule->getExpression());
    }

    public function testHourly()
    {
        $this->schedule->hourly();
        $this->assertEquals('0 * * * *', $this->schedule->getExpression());
    }

    public function testHourlyAt()
    {
        $this->schedule->hourlyAt(15);
        $this->assertEquals('15 * * * *', $this->schedule->getExpression());
    }

    public function testEveryTwoHours()
    {
        $this->schedule->everyTwoHours();
        $this->assertEquals('0 */2 * * *', $this->schedule->getExpression());
    }

    public function testEveryThreeHours()
    {
        $this->schedule->everyThreeHours();
        $this->assertEquals('0 */3 * * *', $this->schedule->getExpression());
    }

    public function testEveryFourHours()
    {
        $this->schedule->everyFourHours();
        $this->assertEquals('0 */4 * * *', $this->schedule->getExpression());
    }

    public function testEverySixHours()
    {
        $this->schedule->everySixHours();
        $this->assertEquals('0 */6 * * *', $this->schedule->getExpression());
    }

    public function testDaily()
    {
        $this->schedule->daily();
        $this->assertEquals('0 0 * * *', $this->schedule->getExpression());
    }

    public function testDailyAt()
    {
        $this->schedule->dailyAt('13:30');
        $this->assertEquals('30 13 * * *', $this->schedule->getExpression());
    }

    public function testTwiceDaily()
    {
        $this->schedule->twiceDaily(4, 16);
        $this->assertEquals('0 4,16 * * *', $this->schedule->getExpression());
    }

    public function testWeekly()
    {
        $this->schedule->weekly();
        $this->assertEquals('0 0 * * 0', $this->schedule->getExpression());
    }

    public function testWeeklyOn()
    {
        $this->schedule->weeklyOn(1, '15:45');
        $this->assertEquals('45 15 * * 1', $this->schedule->getExpression());
    }

    public function testMonthly()
    {
        $this->schedule->monthly();
        $this->assertEquals('0 0 1 * *', $this->schedule->getExpression());
    }

    public function testMonthlyOn()
    {
        $this->schedule->monthlyOn(15, '10:00');
        $this->assertEquals('0 10 15 * *', $this->schedule->getExpression());
    }

    public function testTwiceMonthly()
    {
        $this->schedule->twiceMonthly(1, 15, '12:00');
        $this->assertEquals('0 12 1,15 * *', $this->schedule->getExpression());
    }

    public function testQuarterly()
    {
        $this->schedule->quarterly();
        $this->assertEquals('0 0 1 1-12/3 *', $this->schedule->getExpression());
    }

    public function testYearly()
    {
        $this->schedule->yearly();
        $this->assertEquals('0 0 1 1 *', $this->schedule->getExpression());
    }

    public function testWeekdays()
    {
        $this->schedule->weekdays();
        $this->assertEquals('0 0 * * 1-5', $this->schedule->getExpression());
    }

    public function testWeekends()
    {
        $this->schedule->weekends();
        $this->assertEquals('0 0 * * 0,6', $this->schedule->getExpression());
    }

    public function testMondays()
    {
        $this->schedule->mondays();
        $this->assertEquals('0 0 * * 1', $this->schedule->getExpression());
    }

    public function testTuesdays()
    {
        $this->schedule->tuesdays();
        $this->assertEquals('0 0 * * 2', $this->schedule->getExpression());
    }

    public function testWednesdays()
    {
        $this->schedule->wednesdays();
        $this->assertEquals('0 0 * * 3', $this->schedule->getExpression());
    }

    public function testThursdays()
    {
        $this->schedule->thursdays();
        $this->assertEquals('0 0 * * 4', $this->schedule->getExpression());
    }

    public function testFridays()
    {
        $this->schedule->fridays();
        $this->assertEquals('0 0 * * 5', $this->schedule->getExpression());
    }

    public function testSaturdays()
    {
        $this->schedule->saturdays();
        $this->assertEquals('0 0 * * 6', $this->schedule->getExpression());
    }

    public function testSundays()
    {
        $this->schedule->sundays();
        $this->assertEquals('0 0 * * 0', $this->schedule->getExpression());
    }

    public function testDaysWithArray()
    {
        $this->schedule->days([1, 3, 5]);
        $this->assertEquals('0 0 * * 1,3,5', $this->schedule->getExpression());
    }

    public function testAtOverridesTime()
    {
        $this->schedule->weeklyOn(1)->at('14:30');
        $this->assertEquals('30 14 * * 1', $this->schedule->getExpression());
    }

    public function testCronSchedulesCustomExpression()
    {
        $this->schedule->cron('1 2 3 4 5');
        $this->assertEquals('1 2 3 4 5', $this->schedule->getExpression());
    }

    public function testBuildSetsTask()
    {
        $callback = function () {
        };
        $task = $this->schedule->everyMinute()->call($callback)->build();

        $this->assertInstanceOf(CronTask::class, $task);
        $this->assertEquals('test-task', $task->getName());
        $this->assertEquals('* * * * *', $task->getExpression());
    }

    public function testBuildThrowsExceptionWhenCallbackMissing()
    {
        $this->expectException(CronException::class);
        $this->expectExceptionMessage("Task 'test-task' must have a callback. Use call() method.");
        $this->schedule->everyMinute()->build();
    }

    public function testBuildThrowsExceptionWhenScheduleMissing()
    {
        $callback = function () {
        };
        $this->expectException(CronException::class);
        $this->expectExceptionMessage("Task 'test-task' must have a schedule. Use methods like daily(), hourly(), etc.");
        $this->schedule->call($callback)->build();
    }
}
