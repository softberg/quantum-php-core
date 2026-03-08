<?php

namespace Quantum\Tests\Unit\Logger\Adapters;

use Quantum\Logger\Adapters\MessageAdapter;
use Quantum\Debugger\DebuggerStore;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Debugger\Debugger;

class MessageAdapterTest extends AppTestCase
{
    private $adapter;
    private $debugger;

    public function setUp(): void
    {
        parent::setUp();

        $store = new DebuggerStore();

        $this->debugger = Debugger::getInstance($store);

        $this->debugger->initStore();

        $this->adapter = new MessageAdapter();
    }

    public function testReportAddsMessageToDebuggerStore()
    {
        $level = 'info';
        $message = 'Test message';

        $this->adapter->report($level, $message);

        $storedMessages = $this->debugger->getStoreCell(Debugger::MESSAGES);

        $this->assertArrayHasKey($level, $storedMessages[0]);

        $this->assertEquals($message, $storedMessages[0][$level]);
    }
}
