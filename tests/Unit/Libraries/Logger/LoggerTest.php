<?php

namespace Quantum\Tests\Unit\Libraries\Logger;

use Quantum\Libraries\Logger\Contracts\ReportableInterface;
use Quantum\Libraries\Logger\Adapters\MessageAdapter;
use Quantum\Libraries\Logger\Logger;
use Quantum\Debugger\DebuggerStore;
use Quantum\Debugger\Debugger;
use Quantum\Tests\Unit\AppTestCase;

class LoggerTest extends AppTestCase
{

    private $logger;
    private $adapter;
    private $debugger;

    public function setUp(): void
    {
        parent::setUp();

        $store = new DebuggerStore();

        $this->debugger = Debugger::getInstance($store);

        $this->debugger->initStore();

        $this->adapter = new MessageAdapter();

        $this->logger = new Logger($this->adapter);
    }

    public function testLoggerGetAdapter()
    {
        $this->assertEquals($this->adapter, $this->logger->getAdapter());

        $this->assertInstanceOf(MessageAdapter::class, $this->logger->getAdapter());

        $this->assertInstanceOf(ReportableInterface::class, $this->logger->getAdapter());
    }

    public function testLoggerLogAddsMessageToDebuggerStore()
    {
        $levelInfo = 'info';
        $messageInfo = 'Test message';

        $levelError = 'error';
        $messageError = 'Error message';

        $context = [];

        $this->logger->log($levelInfo, $messageInfo, $context);

        $storedMessages = $this->debugger->getStoreCell(Debugger::MESSAGES);

        $this->assertArrayHasKey($levelInfo, $storedMessages[0]);
        $this->assertEquals($messageInfo, $storedMessages[0][$levelInfo]);

        $this->logger->log($levelError, $messageError, $context);

        $storedMessages = $this->debugger->getStoreCell(Debugger::MESSAGES);

        $this->assertArrayHasKey($levelError, $storedMessages[1]);
        $this->assertEquals($messageError, $storedMessages[1][$levelError]);
    }
}