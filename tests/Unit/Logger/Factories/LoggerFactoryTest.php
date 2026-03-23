<?php

namespace Quantum\Tests\Unit\Logger\Factories;

use Quantum\Logger\Contracts\ReportableInterface;
use Quantum\Logger\Exceptions\LoggerException;
use Quantum\Logger\Adapters\MessageAdapter;
use Quantum\Logger\Factories\LoggerFactory;
use Quantum\Logger\Adapters\SingleAdapter;
use Quantum\Logger\Adapters\DailyAdapter;
use Quantum\Logger\Enums\LoggerType;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Logger\Logger;

class LoggerFactoryTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config()->set('app.debug', false);

        $this->setPrivateProperty(LoggerFactory::class, 'instances', []);
    }

    public function testLoggerFactoryInstance(): void
    {
        $logger = LoggerFactory::get();

        $this->assertInstanceOf(Logger::class, $logger);
    }

    public function testLoggerFactoryGetDefaultAdapter(): void
    {
        $logger = LoggerFactory::get();

        $this->assertInstanceOf(SingleAdapter::class, $logger->getAdapter());

        $this->assertInstanceOf(ReportableInterface::class, $logger->getAdapter());
    }

    public function testLoggerFactoryGetSingleAdapter(): void
    {
        $logger = LoggerFactory::get(LoggerType::SINGLE);

        $this->assertInstanceOf(SingleAdapter::class, $logger->getAdapter());

        $this->assertInstanceOf(ReportableInterface::class, $logger->getAdapter());
    }

    public function testLoggerFactoryGetDailyAdapter(): void
    {
        $logger = LoggerFactory::get(LoggerType::DAILY);

        $this->assertInstanceOf(DailyAdapter::class, $logger->getAdapter());

        $this->assertInstanceOf(ReportableInterface::class, $logger->getAdapter());
    }

    public function testLoggerFactoryGetMessageAdapter(): void
    {
        config()->set('app.debug', true);

        $logger = LoggerFactory::get();

        $this->assertInstanceOf(MessageAdapter::class, $logger->getAdapter());

        $this->assertInstanceOf(ReportableInterface::class, $logger->getAdapter());
    }

    public function testLoggerFactoryTryingToGetMessageAdapter(): void
    {
        $this->expectException(LoggerException::class);

        $this->expectExceptionMessage('The adapter `message` is not supported.');

        LoggerFactory::get(LoggerType::MESSAGE);
    }

    public function testLoggerFactoryInvalidTypeAdapter(): void
    {
        $this->expectException(LoggerException::class);

        $this->expectExceptionMessage('The adapter `invalid_type` is not supported');

        LoggerFactory::get('invalid_type');
    }

    public function testLoggerFactoryReturnsSameInstance(): void
    {
        $logger1 = LoggerFactory::get(LoggerType::SINGLE);
        $logger2 = LoggerFactory::get(LoggerType::SINGLE);

        $this->assertSame($logger1, $logger2);
    }
}
