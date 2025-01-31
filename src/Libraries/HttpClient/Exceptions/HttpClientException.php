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

namespace Quantum\Libraries\HttpClient\Exceptions;

use Quantum\Exceptions\BaseException;

/**
 * Class HasherException
 * @package Quantum\Libraries\Hasher
 */
class HttpClientException extends BaseException
{

    /**
     * @param string $name
     * @return HttpClientException
     */
    public static function methodNotAvailable(string $name): self
    {
        return new static(t('exception.method_not_available', $name), E_WARNING);
    }

    /**
     * @return HttpClientException
     */
    public static function requestNotCreated(): self
    {
        return new static(t('exception.request_not_created'), E_WARNING);
    }
}