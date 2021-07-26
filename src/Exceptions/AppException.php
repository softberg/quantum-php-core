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
 * Class AppException
 * @package Quantum\Exceptions
 */
class AppException extends \Exception
{

    /**
     * App key is missing
     */
    const APP_KEY_MISSING = 'APP KEY is missing';

    /**
     * @return \Quantum\Exceptions\AppException
     */
    public static function missingAppKey(): AppException
    {
        return new static(self::APP_KEY_MISSING, E_ERROR);
    }
}
