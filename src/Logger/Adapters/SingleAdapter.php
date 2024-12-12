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

namespace Quantum\Logger\Adapters;

use Quantum\Libraries\Storage\FileSystem;
use Quantum\Logger\ReportableInterface;
use Quantum\Exceptions\DiException;
use Quantum\Logger\LoggerException;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Class SingleAdapter
 * @package Quantum\Logger
 */
class SingleAdapter implements ReportableInterface
{

    /**
     * @var FileSystem
     */
    private $fs;

    /**
     * @var string
     */
    private $logFile;

    /**
     * SingleAdapter constructor.
     * @param array $params
     * @throws DiException
     * @throws LoggerException
     * @throws ReflectionException
     * @throws \Quantum\Exceptions\LangException
     */
    public function __construct(array $params)
    {
        $this->fs = Di::get(FileSystem::class);

        if (!$this->fs->extension($params['path'])) {
            throw LoggerException::logPathIsNotFile($params['path']);
        }

        $this->logFile = $params['path'];
    }

    /**
     * @inheritDoc
     */
    public function report(string $level, $message, ?array $context = [])
    {
        $this->fs->append($this->logFile, $this->formatMessage($level, $message, $context));
    }

    /**
     * @param string $level
     * @param $message
     * @param array|null $context
     * @return string
     */
    protected function formatMessage(string $level, $message, ?array $context = []): string
    {
        return sprintf(
            "[%s] %s: %s%s",
            date('Y-m-d H:i:s'),
            ucfirst($level),
            is_array($message) ? json_encode($message, JSON_PRETTY_PRINT) : $message,
            isset($context['trace']) ? (PHP_EOL . $context['trace'] . PHP_EOL) : PHP_EOL
        );
    }

}