<?php

namespace Quantum\Tests\Unit\Libraries\Logger\Adapters;

use Quantum\Libraries\Logger\Exceptions\LoggerException;
use Quantum\Libraries\Logger\Adapters\DailyAdapter;
use Quantum\Tests\Unit\AppTestCase;

class DailyAdapterTest extends AppTestCase
{
    private $adapter;

    public function setUp(): void
    {
        parent::setUp();

        $this->adapter = new DailyAdapter([
            'path' => base_dir() . DS . 'logs',
        ]);
    }

    public function testDailyAdapterInstance()
    {
        $this->assertInstanceOf(DailyAdapter::class, $this->adapter);
    }

    public function testDailyAdapterConstructorThrowsExceptionForInvalidPath()
    {
        $this->expectException(LoggerException::class);

        new DailyAdapter([
            'path' => base_dir() . DS . 'invalid_path',
        ]);
    }

    public function testDailyAdapterReportWritesToFile()
    {
        $level = 'info';
        $message = 'Test log message';
        $context = [];

        $this->adapter->report($level, $message, $context);

        $logFile = base_dir() . DS . 'logs' . DS . date('Y-m-d') . '.log';

        $this->assertTrue($this->fs->exists($logFile));

        $logContent = $this->fs->get($logFile);
        $this->assertStringContainsString('Test log message', $logContent);

        $this->fs->remove($logFile);
    }
}
