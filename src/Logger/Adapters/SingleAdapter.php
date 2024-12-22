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
 * Class SingleAdapter
 * @package Quantum\Logger
 */
class SingleAdapter extends BaseLogger
{

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