<?php

namespace Quantum\Tests\Unit\Libraries\Logger\Adapters;

use Quantum\Libraries\Logger\Exceptions\LoggerException;
use Quantum\Libraries\Logger\Adapters\SingleAdapter;
use Quantum\Tests\Unit\AppTestCase;

class SingleAdapterTest extends AppTestCase
{
    private $adapter;

    public function setUp(): void
    {
        parent::setUp();

        $this->adapter = new SingleAdapter([
            'path' => base_dir() . DS . 'logs' . DS . 'app.log',
        ]);
    }

    public function testSingleAdapterInstance()
    {
        $this->assertInstanceOf(SingleAdapter::class, $this->adapter);
    }

    public function testSingleAdapterConstructorThrowsExceptionForInvalidPath()
    {
        $this->expectException(LoggerException::class);

        new SingleAdapter([
            'path' => base_dir() . DS . 'invalid_path',
        ]);
    }

    public function testSingleAdapterReportWritesToFile()
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
