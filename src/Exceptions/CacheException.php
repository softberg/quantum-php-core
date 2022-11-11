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
 * @since 2.8.0
 */

namespace Quantum\Exceptions;

/**
 * Class CacheException
 * @package Quantum\Exceptions
 */
class CacheException extends AppException
{
    /**
     * @param string $name
     * @return CacheException
     */
    public static function cantConnect(string $name): CacheException
    {
        return new static(t('exception.cant_connect', $name));
    }
}
