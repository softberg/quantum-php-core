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

namespace Quantum\App\Exceptions;

use Quantum\Exceptions\BaseException;

/**
 * Class AppException
 * @package Quantum\Exceptions
 */
class AppException extends BaseException
{

    /**
     * @return AppException
     */
    public static function missingAppKey(): AppException
    {
        return new static(t('exception.app_key_missing'), E_ERROR);
    }
}
