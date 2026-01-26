<?php

namespace Quantum\Tests\Unit\Libraries\Cron;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Libraries\Cron\CronManager;
use Quantum\Libraries\Cron\CronTask;
use Quantum\Libraries\Cron\Schedule;

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

    public function testCronConfig()
    {
        $this->assertEquals('/default/path', cron_config('path'));
        $this->assertEquals('default-val', cron_config('non-existent', 'default-val'));
    }

    public function testCronManagerHelper()
    {
        $manager = cron_manager('/custom/path');
        $this->assertInstanceOf(CronManager::class, $manager);
    }

    public function testCronTaskHelper()
    {
        $task = cron_task('my-task', '* * * * *', function () {
        });
        $this->assertInstanceOf(CronTask::class, $task);
        $this->assertEquals('my-task', $task->getName());
    }

    public function testScheduleHelper()
    {
        $schedule = schedule('my-task');
        $this->assertInstanceOf(Schedule::class, $schedule);
    }
}
