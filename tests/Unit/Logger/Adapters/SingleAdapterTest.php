<?php

namespace Quantum\Tests\Unit\Logger\Adapters;

use Quantum\Logger\Exceptions\LoggerException;
use Quantum\Logger\Adapters\SingleAdapter;
use Quantum\Tests\Unit\AppTestCase;

class SingleAdapterTest extends AppTestCase
{
    private SingleAdapter $adapter;

    public function setUp(): void
    {
        parent::setUp();

        $this->adapter = new SingleAdapter([
            'path' => base_dir() . DS . 'logs' . DS . 'app.log',
        ]);
    }

    public function testSingleAdapterInstance(): void
    {
        $this->assertInstanceOf(SingleAdapter::class, $this->adapter);
    }

    public function testSingleAdapterConstructorThrowsExceptionForInvalidPath(): void
    {
        $this->expectException(LoggerException::class);

        new SingleAdapter([
            'path' => base_dir() . DS . 'invalid_path',
        ]);
    }

    public function testSingleAdapterReportWritesToFile(): void
    {
        $level = 'info';
        $message = 'Test log message';
        $context = [];

        $this->adapter->report($level, $message, $context);

        $logFile = base_dir() . DS . 'logs' . DS . 'app.log';

        $this->assertTrue($this->fs->exists($logFile));

        $logContent = $this->fs->get($logFile);
        $this->assertStringContainsString('Test log message', $logContent);

        $this->fs->remove($logFile);
    }
}
