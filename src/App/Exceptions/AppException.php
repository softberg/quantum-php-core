<?php

declare(strict_types=1);

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

namespace Quantum\App\Exceptions;

use Quantum\App\Enums\ExceptionMessages;

/**
 * Class AppException
 * @package Quantum\App
 */
class AppException extends BaseException
{

    /**
     * @return AppException
     */
    public static function missingAppKey(): AppException
    {
        return new static(ExceptionMessages::APP_KEY_MISSING, E_ERROR);
    }
}
