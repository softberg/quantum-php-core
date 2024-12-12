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
use Quantum\Logger\ReportableInterface;
use Quantum\Logger\LoggerFactory;
use Quantum\Tests\AppTestCase;
use Quantum\Logger\Logger;
use Mockery;

class LoggerFactoryTest extends AppTestCase
{

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testCreateLoggerWithCustomAdapter()
    {
        $mockAdapter = Mockery::mock(ReportableInterface::class);

        $logger = LoggerFactory::createLogger($mockAdapter);

        $this->assertInstanceOf(Logger::class, $logger);

        $this->assertSame($mockAdapter, $logger->getAdapter());
    }

    public function testCreateLoggerWithDefaultAdapter()
    {
        $logger = LoggerFactory::createLogger();

        $this->assertInstanceOf(Logger::class, $logger);

        $this->assertInstanceOf(MessageAdapter::class, $logger->getAdapter());
    }

}
