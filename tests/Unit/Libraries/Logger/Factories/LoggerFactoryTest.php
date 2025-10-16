<?php

namespace Quantum\Tests\Unit\Libraries\Logger\Factories;

use Quantum\Libraries\Logger\Contracts\ReportableInterface;
use Quantum\Libraries\Logger\Exceptions\LoggerException;
use Quantum\Libraries\Logger\Adapters\MessageAdapter;
use Quantum\Libraries\Logger\Factories\LoggerFactory;
use Quantum\Libraries\Logger\Adapters\SingleAdapter;
use Quantum\Libraries\Logger\Adapters\DailyAdapter;
use Quantum\Libraries\Logger\Logger;
use Quantum\Tests\Unit\AppTestCase;

class LoggerFactoryTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        config()->set('app.debug', false);

        $this->setPrivateProperty(LoggerFactory::class, 'instances', []);
    }

    public function testLoggerFactoryInstance()
    {
        $logger = LoggerFactory::get();

        $this->assertInstanceOf(Logger::class, $logger);
    }

    public function testLoggerFactoryGetDefaultAdapter()
    {
        $logger = LoggerFactory::get();

        $this->assertInstanceOf(SingleAdapter::class, $logger->getAdapter());

        $this->assertInstanceOf(ReportableInterface::class, $logger->getAdapter());
    }

    public function testLoggerFactoryGetSingleAdapter()
    {
        $logger = LoggerFactory::get(Logger::SINGLE);

        $this->assertInstanceOf(SingleAdapter::class, $logger->getAdapter());

        $this->assertInstanceOf(ReportableInterface::class, $logger->getAdapter());
    }

    public function testLoggerFactoryGetDailyAdapter()
    {
        $logger = LoggerFactory::get(Logger::DAILY);

        $this->assertInstanceOf(DailyAdapter::class, $logger->getAdapter());

        $this->assertInstanceOf(ReportableInterface::class, $logger->getAdapter());
    }

    public function testLoggerFactoryGetMessageAdapter()
    {
        config()->set('app.debug', true);

        $logger = LoggerFactory::get();

        $this->assertInstanceOf(MessageAdapter::class, $logger->getAdapter());

        $this->assertInstanceOf(ReportableInterface::class, $logger->getAdapter());
    }

    public function testLoggerFactoryTryingToGetMessageAdapter()
    {
        $this->expectException(LoggerException::class);

        $this->expectExceptionMessage('exception.message_logger_not_in_debug_mode');

        LoggerFactory::get(Logger::MESSAGE);
    }

    public function testLoggerFactoryInvalidTypeAdapter()
    {
        $this->expectException(LoggerException::class);

        $this->expectExceptionMessage('The adapter `invalid_type` is not supported`');

        LoggerFactory::get('invalid_type');
    }

    public function testLoggerFactoryReturnsSameInstance()
    {
        $logger1 = LoggerFactory::get(Logger::SINGLE);
        $logger2 = LoggerFactory::get(Logger::SINGLE);

        $this->assertSame($logger1, $logger2);
    }
}