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
 * Class DailyAdapter
 * @package Quantum\Logger
 */
class DailyAdapter implements ReportableInterface
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
     * Initialize the adapter for Daily logs
     * @param array $params
     * @return void
     * @throws LoggerException
     */
    protected function initialize(array $params): void
    {
        if (!$this->fs->isDirectory($params['path'])) {
            throw LoggerException::logPathIsNotDirectory($params['path']);
        }

        $this->logFile = $params['path'] . DS . date('Y-m-d') . '.log';
    }
}
