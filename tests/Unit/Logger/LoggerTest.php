<?php

namespace Quantum\Tests\Unit\Logger;

use Quantum\Logger\Contracts\ReportableInterface;
use Quantum\Logger\Adapters\MessageAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Debugger\Debugger;
use Quantum\Logger\Logger;
use Quantum\Di\Di;

class LoggerTest extends AppTestCase
{
    private Logger $logger;
    private MessageAdapter $adapter;
    private Debugger $debugger;

    public function setUp(): void
    {
        parent::setUp();

        if (!Di::isRegistered(Debugger::class)) {
            Di::register(Debugger::class);
        }

        $this->debugger = Di::get(Debugger::class);

        $this->debugger->initStore();

        $this->adapter = new MessageAdapter();

        $this->logger = new Logger($this->adapter);
    }

    public function testLoggerGetAdapter(): void
    {
        $this->assertEquals($this->adapter, $this->logger->getAdapter());

        $this->assertInstanceOf(MessageAdapter::class, $this->logger->getAdapter());

        $this->assertInstanceOf(ReportableInterface::class, $this->logger->getAdapter());
    }

    public function testLoggerLogAddsMessageToDebuggerStore(): void
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
