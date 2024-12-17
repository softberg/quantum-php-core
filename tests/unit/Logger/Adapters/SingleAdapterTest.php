<?php

namespace Quantum\Tests\Logger\Adapters;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Logger\Adapters\SingleAdapter;
use Quantum\Logger\LoggerException;
use Quantum\Tests\AppTestCase;
use Quantum\Di\Di;

class SingleAdapterTest extends AppTestCase
{
    private $adapter;
    private $fileSystem;

    public function setUp(): void
    {
        parent::setUp();

        $this->fileSystem = Di::get(FileSystem::class);

        $this->adapter = new SingleAdapter([
            'path' => base_dir() . DS . 'logs' . DS . 'app.log',
        ]);
    }

    public function testSingleAdapterInstance()
    {
        // Ensure that the adapter is an instance of SingleAdapter
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

        $this->assertTrue($this->fileSystem->exists($logFile));

        $logContent = $this->fileSystem->get($logFile);
        $this->assertStringContainsString('Test log message', $logContent);

        $this->fileSystem->remove($logFile);
    }

}
