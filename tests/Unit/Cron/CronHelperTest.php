<?php

namespace Quantum\Tests\Unit\Cron;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Cron\CronManager;
use Quantum\Cron\CronTask;
use Quantum\Cron\Schedule;

class CronHelperTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!config()->has('cron')) {
            config()->set('cron', [
                'path' => '/default/path',
                'lock_path' => '/default/lock',
            ]);
        }
    }

    public function testCronConfig(): void
    {
        $this->assertEquals('/default/path', cron_config('path'));
        $this->assertEquals('default-val', cron_config('non-existent', 'default-val'));
    }

    public function testCronManagerHelper(): void
    {
        $manager = cron_manager('/custom/path');
        $this->assertInstanceOf(CronManager::class, $manager);
    }

    public function testCronTaskHelper(): void
    {
        $task = cron_task('my-task', '* * * * *', function (): void {
        });
        $this->assertInstanceOf(CronTask::class, $task);
        $this->assertEquals('my-task', $task->getName());
    }

    public function testScheduleHelper(): void
    {
        $schedule = schedule('my-task');
        $this->assertInstanceOf(Schedule::class, $schedule);
    }
}
