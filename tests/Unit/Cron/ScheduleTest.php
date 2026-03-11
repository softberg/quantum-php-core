<?php

namespace Quantum\Tests\Unit\Cron;

use Quantum\Cron\Exceptions\CronException;
use PHPUnit\Framework\TestCase;
use Quantum\Cron\Schedule;
use Quantum\Cron\CronTask;

class ScheduleTest extends TestCase
{
    private Schedule $schedule;

    public function setUp(): void
    {
        $this->schedule = new Schedule('test-task');
    }

    public function testEveryMinute(): void
    {
        $this->schedule->everyMinute();
        $this->assertEquals('* * * * *', $this->schedule->getExpression());
    }

    public function testEveryFiveMinutes(): void
    {
        $this->schedule->everyFiveMinutes();
        $this->assertEquals('*/5 * * * *', $this->schedule->getExpression());
    }

    public function testEveryTenMinutes(): void
    {
        $this->schedule->everyTenMinutes();
        $this->assertEquals('*/10 * * * *', $this->schedule->getExpression());
    }

    public function testEveryFifteenMinutes(): void
    {
        $this->schedule->everyFifteenMinutes();
        $this->assertEquals('*/15 * * * *', $this->schedule->getExpression());
    }

    public function testEveryThirtyMinutes(): void
    {
        $this->schedule->everyThirtyMinutes();
        $this->assertEquals('*/30 * * * *', $this->schedule->getExpression());
    }

    public function testHourly(): void
    {
        $this->schedule->hourly();
        $this->assertEquals('0 * * * *', $this->schedule->getExpression());
    }

    public function testHourlyAt(): void
    {
        $this->schedule->hourlyAt(15);
        $this->assertEquals('15 * * * *', $this->schedule->getExpression());
    }

    public function testEveryTwoHours(): void
    {
        $this->schedule->everyTwoHours();
        $this->assertEquals('0 */2 * * *', $this->schedule->getExpression());
    }

    public function testEveryThreeHours(): void
    {
        $this->schedule->everyThreeHours();
        $this->assertEquals('0 */3 * * *', $this->schedule->getExpression());
    }

    public function testEveryFourHours(): void
    {
        $this->schedule->everyFourHours();
        $this->assertEquals('0 */4 * * *', $this->schedule->getExpression());
    }

    public function testEverySixHours(): void
    {
        $this->schedule->everySixHours();
        $this->assertEquals('0 */6 * * *', $this->schedule->getExpression());
    }

    public function testDaily(): void
    {
        $this->schedule->daily();
        $this->assertEquals('0 0 * * *', $this->schedule->getExpression());
    }

    public function testDailyAt(): void
    {
        $this->schedule->dailyAt('13:30');
        $this->assertEquals('30 13 * * *', $this->schedule->getExpression());
    }

    public function testTwiceDaily(): void
    {
        $this->schedule->twiceDaily(4, 16);
        $this->assertEquals('0 4,16 * * *', $this->schedule->getExpression());
    }

    public function testWeekly(): void
    {
        $this->schedule->weekly();
        $this->assertEquals('0 0 * * 0', $this->schedule->getExpression());
    }

    public function testWeeklyOn(): void
    {
        $this->schedule->weeklyOn(1, '15:45');
        $this->assertEquals('45 15 * * 1', $this->schedule->getExpression());
    }

    public function testMonthly(): void
    {
        $this->schedule->monthly();
        $this->assertEquals('0 0 1 * *', $this->schedule->getExpression());
    }

    public function testMonthlyOn(): void
    {
        $this->schedule->monthlyOn(15, '10:00');
        $this->assertEquals('0 10 15 * *', $this->schedule->getExpression());
    }

    public function testTwiceMonthly(): void
    {
        $this->schedule->twiceMonthly(1, 15, '12:00');
        $this->assertEquals('0 12 1,15 * *', $this->schedule->getExpression());
    }

    public function testQuarterly(): void
    {
        $this->schedule->quarterly();
        $this->assertEquals('0 0 1 1-12/3 *', $this->schedule->getExpression());
    }

    public function testYearly(): void
    {
        $this->schedule->yearly();
        $this->assertEquals('0 0 1 1 *', $this->schedule->getExpression());
    }

    public function testWeekdays(): void
    {
        $this->schedule->weekdays();
        $this->assertEquals('0 0 * * 1-5', $this->schedule->getExpression());
    }

    public function testWeekends(): void
    {
        $this->schedule->weekends();
        $this->assertEquals('0 0 * * 0,6', $this->schedule->getExpression());
    }

    public function testMondays(): void
    {
        $this->schedule->mondays();
        $this->assertEquals('0 0 * * 1', $this->schedule->getExpression());
    }

    public function testTuesdays(): void
    {
        $this->schedule->tuesdays();
        $this->assertEquals('0 0 * * 2', $this->schedule->getExpression());
    }

    public function testWednesdays(): void
    {
        $this->schedule->wednesdays();
        $this->assertEquals('0 0 * * 3', $this->schedule->getExpression());
    }

    public function testThursdays(): void
    {
        $this->schedule->thursdays();
        $this->assertEquals('0 0 * * 4', $this->schedule->getExpression());
    }

    public function testFridays(): void
    {
        $this->schedule->fridays();
        $this->assertEquals('0 0 * * 5', $this->schedule->getExpression());
    }

    public function testSaturdays(): void
    {
        $this->schedule->saturdays();
        $this->assertEquals('0 0 * * 6', $this->schedule->getExpression());
    }

    public function testSundays(): void
    {
        $this->schedule->sundays();
        $this->assertEquals('0 0 * * 0', $this->schedule->getExpression());
    }

    public function testDaysWithArray(): void
    {
        $this->schedule->days([1, 3, 5]);
        $this->assertEquals('0 0 * * 1,3,5', $this->schedule->getExpression());
    }

    public function testAtOverridesTime(): void
    {
        $this->schedule->weeklyOn(1)->at('14:30');
        $this->assertEquals('30 14 * * 1', $this->schedule->getExpression());
    }

    public function testCronSchedulesCustomExpression(): void
    {
        $this->schedule->cron('1 2 3 4 5');
        $this->assertEquals('1 2 3 4 5', $this->schedule->getExpression());
    }

    public function testBuildSetsTask(): void
    {
        $callback = function (): void {
        };
        $task = $this->schedule->everyMinute()->call($callback)->build();

        $this->assertInstanceOf(CronTask::class, $task);
        $this->assertEquals('test-task', $task->getName());
        $this->assertEquals('* * * * *', $task->getExpression());
    }

    public function testBuildThrowsExceptionWhenCallbackMissing(): void
    {
        $this->expectException(CronException::class);
        $this->expectExceptionMessage("Task 'test-task' must have a callback. Use call() method.");
        $this->schedule->everyMinute()->build();
    }

    public function testBuildThrowsExceptionWhenScheduleMissing(): void
    {
        $callback = function (): void {
        };
        $this->expectException(CronException::class);
        $this->expectExceptionMessage("Task 'test-task' must have a schedule. Use methods like daily(), hourly(), etc.");
        $this->schedule->call($callback)->build();
    }
}
