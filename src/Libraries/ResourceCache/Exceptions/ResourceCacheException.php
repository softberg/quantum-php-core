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
 * @since 2.9.9
 */

namespace Quantum\Libraries\ResourceCache\Exceptions;

use Quantum\Libraries\ResourceCache\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class JwtException
 * @package Quantum\Libraries\JwtToken
 */
class ResourceCacheException extends BaseException
{

    /**
     * @param string $className
     * @param string $packageName
     * @return ResourceCacheException
     */
    public static function classNotFound(string $className, string $packageName): ResourceCacheException
    {
        return new static(_message(ExceptionMessages::TRANSLATION_FILES_NOT_FOUND, [$className, $packageName]), E_WARNING);
    }
}