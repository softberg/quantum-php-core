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
 * @since 3.0.0
 */

namespace Quantum\Libraries\Logger\Traits;

use Quantum\Libraries\Logger\Exceptions\LoggerException;
use Quantum\Libraries\Storage\FileSystem;

/**
 * Trait LoggerTrait
 * @package Quantum\Logger
 */
trait LoggerTrait
{
    /**
     * @var FileSystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $logFile;

    /**
     * Initialize the logger
     * @param array $params
     * @throws LoggerException
     */
    abstract protected function initialize(array $params): void;

    /**
     * Reports a log message
     * @param string $level
     * @param $message
     * @param array|null $context
     * @return void
     */
    public function report(string $level, $message, ?array $context = []): void
    {
        $this->fs->append($this->logFile, $this->formatMessage($level, $message, $context));
    }

    /**
     * Formats the log message
     * @param string $level
     * @param $message
     * @param array|null $context
     * @return string
     */
    protected function formatMessage(string $level, $message, ?array $context = []): string
    {
        return sprintf(
            '[%s] %s: %s%s',
            date('Y-m-d H:i:s'),
            ucfirst($level),
            is_array($message) ? json_encode($message, JSON_PRETTY_PRINT) : $message,
            isset($context['trace']) ? (PHP_EOL . $context['trace'] . PHP_EOL) : PHP_EOL
        );
    }
}
