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

namespace Quantum\Tests\Libraries\Logger;

use Quantum\Libraries\Logger\LoggerConfig;
use Quantum\Tests\AppTestCase;

class LoggerConfigTest extends AppTestCase
{
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