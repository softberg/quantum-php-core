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

use Quantum\App\Exceptions\BaseException;
use Quantum\App\Exceptions\AppException;
use Quantum\Libraries\Csrf\Csrf;

/**
 * Gets the Csrf instance
 * @return Csrf
 */
function csrf(): Csrf
{
    return Csrf::getInstance();
}

/**
 * Generates the CSRF token
 * @return string|null
 * @throws BaseException
 */
function csrf_token(): ?string
{
    $appKey = env('APP_KEY');

    if (!$appKey) {
        throw AppException::missingAppKey();
    }

    return csrf()->generateToken($appKey);
}
