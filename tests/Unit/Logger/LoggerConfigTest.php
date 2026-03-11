<?php

namespace Quantum\Tests\Unit\Logger;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Logger\LoggerConfig;

class LoggerConfigTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setPrivateProperty(LoggerConfig::class, 'logLevel', 'error');
    }

    public function testDefaultLogLevel(): void
    {
        $this->assertEquals('error', LoggerConfig::DEFAULT_LOG_LEVEL);
    }

    public function testGetAppLogLevelWithDefault(): void
    {
        $this->assertEquals(400, LoggerConfig::getAppLogLevel());
    }

    public function testSetGetAppLogLevel(): void
    {
        LoggerConfig::setAppLogLevel('debug');

        $this->assertEquals(100, LoggerConfig::getAppLogLevel());

        LoggerConfig::setAppLogLevel('warning');

        $this->assertEquals(300, LoggerConfig::getAppLogLevel());

        LoggerConfig::setAppLogLevel('critical');

        $this->assertEquals(500, LoggerConfig::getAppLogLevel());
    }

    public function testGetLogLevel(): void
    {
        $this->assertEquals(500, LoggerConfig::getLogLevel('critical'));

        $this->assertEquals(300, LoggerConfig::getLogLevel('warning'));

        $this->assertEquals(600, LoggerConfig::getLogLevel('emergency'));
    }

    public function testGetLogLevelWithInvalidErrorType(): void
    {
        $this->assertEquals(400, LoggerConfig::getLogLevel('non_existing_error'));
    }
}
