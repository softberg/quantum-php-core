<?php

namespace Quantum\Tests\Unit\Libraries\Logger;

use Quantum\Libraries\Logger\LoggerConfig;
use Quantum\Tests\Unit\AppTestCase;

class LoggerConfigTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setPrivateProperty(LoggerConfig::class, 'logLevel', 'error');
    }

    public function testDefaultLogLevel()
    {
        $this->assertEquals('error', LoggerConfig::DEFAULT_LOG_LEVEL);
    }

    public function testGetAppLogLevelWithDefault()
    {
        $this->assertEquals(400, LoggerConfig::getAppLogLevel());
    }

    public function testSetGetAppLogLevel()
    {
        LoggerConfig::setAppLogLevel('debug');

        $this->assertEquals(100, LoggerConfig::getAppLogLevel());

        LoggerConfig::setAppLogLevel('warning');

        $this->assertEquals(300, LoggerConfig::getAppLogLevel());

        LoggerConfig::setAppLogLevel('critical');

        $this->assertEquals(500, LoggerConfig::getAppLogLevel());
    }

    public function testGetLogLevel()
    {
        $this->assertEquals(500, LoggerConfig::getLogLevel('critical'));

        $this->assertEquals(300, LoggerConfig::getLogLevel('warning'));

        $this->assertEquals(600, LoggerConfig::getLogLevel('emergency'));
    }

    public function testGetLogLevelWithInvalidErrorType()
    {
        $this->assertEquals(400, LoggerConfig::getLogLevel('non_existing_error'));
    }
}
