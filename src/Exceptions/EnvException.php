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
 * @since 2.5.0
 */

namespace Quantum\Exceptions;

/**
 * Class EnvException
 * @package Quantum\Exceptions
 */
class EnvException extends \Exception
{
    /**
     * Env file is not found
     */
    const ENV_FILE_NOT_FOUND = 'ENV file not found';

    /**
     * @return \Quantum\Exceptions\EnvException
     */
    public static function fileNotFound(): EnvException
    {
        return new static(self::ENV_FILE_NOT_FOUND, E_ERROR);
    }
}
