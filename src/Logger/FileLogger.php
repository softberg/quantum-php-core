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
 * @since 2.4.0
 */

namespace Quantum\Logger;


use Quantum\Contracts\ReportableInterface;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Di\Di;

/**
 * Class FileLogger
 * @package Quantum\Logger
 */
class FileLogger implements ReportableInterface
{

    /**
     * @var \Quantum\Libraries\Storage\FileSystem
     */
    private $fs;

    /**
     * @var string
     */
    private $logFile;

    /**
     * FileLogger constructor.
     * @param string $logFile
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    public function __construct(string $logFile)
    {
        $this->logFile = $logFile;
        $this->fs = Di::get(FileSystem::class);
    }

    /**
     * @inheritDoc
     */
    public function report($level, $message, array $context = [])
    {
        $this->fs->append($this->logFile, $message, FILE_APPEND);
    }

}