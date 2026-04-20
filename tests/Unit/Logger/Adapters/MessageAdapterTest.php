<?php

namespace Quantum\Tests\Unit\Logger\Adapters;

use Quantum\Logger\Adapters\MessageAdapter;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Debugger\Debugger;
use Quantum\Di\Di;

class MessageAdapterTest extends AppTestCase
{
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
    }

    public function testReportAddsMessageToDebuggerStore(): void
    {
        $level = 'info';
        $message = 'Test message';

        $this->adapter->report($level, $message);

        $storedMessages = $this->debugger->getStoreCell(Debugger::MESSAGES);

        $this->assertArrayHasKey($level, $storedMessages[0]);

        $this->assertEquals($message, $storedMessages[0][$level]);
    }
}
