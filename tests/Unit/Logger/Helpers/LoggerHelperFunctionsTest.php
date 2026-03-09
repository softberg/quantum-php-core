<?php

namespace Quantum\Tests\Unit\Logger\Helpers;

use Quantum\Logger\Exceptions\LoggerException;
use Quantum\Logger\Adapters\MessageAdapter;
use Quantum\Logger\Adapters\SingleAdapter;
use Quantum\Logger\Adapters\DailyAdapter;
use Quantum\Debugger\DebuggerStore;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Debugger\Debugger;
use Quantum\Logger\Logger;

class LoggerHelperFunctionsTest extends AppTestCase
{
    private Debugger $debugger;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('app.debug', true);

        $store = new DebuggerStore();

        $this->debugger = Debugger::getInstance($store);

        $this->debugger->resetStore();
    }

    public function tearDown(): void
    {
        $this->debugger->resetStore();
    }

    public function testLoggerHelperGetDefaultLoggerAdapter(): void
    {
        config()->set('app.debug', false);

        $logger = logger();

        $this->assertInstanceOf(Logger::class, $logger);

        $this->assertInstanceOf(SingleAdapter::class, $logger->getAdapter());
    }

    public function testLoggerHelperGetSingleLoggerAdapter(): void
    {
        config()->set('app.debug', false);

        $logger = logger(Logger::SINGLE);

        $this->assertInstanceOf(Logger::class, $logger);

        $this->assertInstanceOf(SingleAdapter::class, $logger->getAdapter());
    }

    public function testLoggerHelperGetDailyLoggerAdapter(): void
    {
        config()->set('app.debug', false);

        $logger = logger(Logger::DAILY);

        $this->assertInstanceOf(Logger::class, $logger);

        $this->assertInstanceOf(DailyAdapter::class, $logger->getAdapter());
    }

    public function testLoggerHelperGetMessageLoggerAdapter(): void
    {
        $logger = logger();

        $this->assertInstanceOf(Logger::class, $logger);

        $this->assertInstanceOf(MessageAdapter::class, $logger->getAdapter());
    }

    public function testLoggerHelperTryToGetMessageLoggerAdapterInNonDebugMode(): void
    {
        config()->set('app.debug', false);

        $this->expectException(LoggerException::class);

        $this->expectExceptionMessage('The adapter `message` is not supported');

        logger(Logger::MESSAGE);
    }

    public function testErrorHelper(): void
    {
        error('Fatal Error');

        $storedMessages = $this->debugger->getStoreCell(Debugger::MESSAGES);

        $this->assertArrayHasKey('error', $storedMessages[0]);

        $this->assertEquals('Fatal Error', $storedMessages[0]['error']);
    }

    public function testWarningHelper(): void
    {
        warning('Warning!!');

        $storedMessages = $this->debugger->getStoreCell(Debugger::MESSAGES);

        $this->assertArrayHasKey('warning', $storedMessages[0]);

        $this->assertEquals('Warning!!', $storedMessages[0]['warning']);
    }

    public function testNoticeHelper(): void
    {
        notice('Simple Notice');

        $storedMessages = $this->debugger->getStoreCell(Debugger::MESSAGES);

        $this->assertArrayHasKey('notice', $storedMessages[0]);

        $this->assertEquals('Simple Notice', $storedMessages[0]['notice']);
    }

    public function testInfoHelper(): void
    {
        info('For your information');

        $storedMessages = $this->debugger->getStoreCell(Debugger::MESSAGES);

        $this->assertArrayHasKey('info', $storedMessages[0]);

        $this->assertEquals('For your information', $storedMessages[0]['info']);
    }

    public function testDebugHelper(): void
    {
        debug('Debugging!!');

        $storedMessages = $this->debugger->getStoreCell(Debugger::MESSAGES);

        $this->assertArrayHasKey('debug', $storedMessages[0]);

        $this->assertEquals('Debugging!!', $storedMessages[0]['debug']);
    }
}
