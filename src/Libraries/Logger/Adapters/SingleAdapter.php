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
 * @since 2.9.7
 */

namespace Quantum\Libraries\Logger\Adapters;

use Quantum\Libraries\Logger\Contracts\ReportableInterface;
use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Libraries\Logger\Exceptions\LoggerException;
use Quantum\Libraries\Logger\Traits\LoggerTrait;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use ReflectionException;

/**
 * Class SingleAdapter
 * @package Quantum\Logger
 */
class SingleAdapter implements ReportableInterface
{

    use LoggerTrait;

    /**
     * @param array $params
     * @throws BaseException
     * @throws LoggerException
     * @throws DiException
     * @throws ConfigException
     * @throws ReflectionException
     */
    public function __construct(array $params)
    {
        $this->fs = FileSystemFactory::get();
        $this->initialize($params);
    }

    /**
     * Initialize the adapter for Single log file
     * @param array $params
     * @return void
     * @throws LoggerException
     */
    protected function initialize(array $params): void
    {
        if (!$this->fs->extension($params['path'])) {
            throw LoggerException::logPathIsNotFile($params['path']);
        }

        $this->logFile = $params['path'];
    }
}