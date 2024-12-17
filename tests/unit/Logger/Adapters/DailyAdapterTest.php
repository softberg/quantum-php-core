<?php

namespace Quantum\Tests\Logger\Adapters;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Logger\Adapters\DailyAdapter;
use Quantum\Logger\LoggerException;
use Quantum\Tests\AppTestCase;
use Quantum\Di\Di;

class DailyAdapterTest extends AppTestCase
{

    private $adapter;
    private $fileSystem;

    public function setUp(): void
    {
        parent::setUp();

        $this->fileSystem = Di::get(FileSystem::class);

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

        $this->assertTrue($this->fileSystem->exists($logFile));

        $logContent = $this->fileSystem->get($logFile);
        $this->assertStringContainsString('Test log message', $logContent);

        $this->fileSystem->remove($logFile);
    }

}
