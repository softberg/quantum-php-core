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

namespace Quantum\Libraries\HttpClient\Exceptions;

use Quantum\Libraries\HttpClient\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class HasherException
 * @package Quantum\Libraries\Hasher
 */
class HttpClientException extends BaseException
{

    /**
     * @return HttpClientException
     */

    public static function requestNotCreated(): self
    {
        return new static(ExceptionMessages::REQUEST_NOT_CREATED, E_WARNING);
    }
}