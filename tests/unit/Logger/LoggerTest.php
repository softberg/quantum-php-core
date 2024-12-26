<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.9.5
 */

namespace Quantum\Tests\Logger;

use Quantum\Logger\Adapters\MessageAdapter;
use Quantum\Tests\AppTestCase;
use Quantum\Debugger\Debugger;
use Quantum\Logger\Logger;
use Mockery;

class LoggerTest extends AppTestCase
{

    private $logger;
    private $adapter;
    private $debugger;

    public function setUp(): void
    {
        parent::setUp();

        $this->debugger = Mockery::mock(Debugger::class);

        $this->debugger->shouldReceive('getInstance')->andReturn($this->debugger);

        $this->adapter = new MessageAdapter();

        $this->logger = new Logger($this->adapter);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testLoggerGetAdapter()
    {
        $this->assertEquals($this->adapter, $this->logger->getAdapter());
    }

    public function testLoggerLogAddsMessageToDebuggerStore()
    {
        $level = 'info';
        $message = 'Test message';
        $context = [];

        $this->debugger
            ->shouldReceive('addToStoreCell')
            ->with(Debugger::MESSAGES, $level, $message);

        $this->debugger
            ->shouldReceive('getStoreCell')
            ->andReturn([[$level => $message]]);

        $this->logger->log($level, $message, $context);

        $storedMessages = $this->debugger->getStoreCell(Debugger::MESSAGES);

        $this->assertArrayHasKey($level, $storedMessages[0]);
        $this->assertEquals($message, $storedMessages[0][$level]);
    }
}
