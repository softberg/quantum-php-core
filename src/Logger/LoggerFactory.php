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

namespace Quantum\Logger;

use Quantum\Logger\Adapters\MessageAdapter;

/**
 * Class LoggerFactory
 * @package Quantum\Logger
 */
class LoggerFactory
{
    /**
     * @param ReportableInterface|null $loggerAdapter
     * @return Logger
     */
    public static function createLogger(?ReportableInterface $loggerAdapter = null): Logger
    {
        if ($loggerAdapter === null) {
            $loggerAdapter = new MessageAdapter();
        }

        return new Logger($loggerAdapter);
    }
}