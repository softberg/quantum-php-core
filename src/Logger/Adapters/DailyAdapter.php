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

use Quantum\Logger\LoggerException;

/**
 * Class DailyAdapter
 * @package Quantum\Logger
 */
class DailyAdapter extends BaseLogger
{

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