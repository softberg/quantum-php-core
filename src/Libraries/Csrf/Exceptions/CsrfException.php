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

namespace Quantum\Libraries\Csrf\Exceptions;

use Quantum\Libraries\Csrf\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class CsrfException
 * @package Quantum\Libraries\Csrf
 */
class CsrfException extends BaseException
{
    /**
     * @return CsrfException
     */
    public static function tokenNotFound(): CsrfException
    {
        return new static(ExceptionMessages::CSRF_TOKEN_MISSING, E_WARNING);
    }

    /**
     * @return CsrfException
     */
    public static function tokenNotMatched(): CsrfException
    {
        return new static(ExceptionMessages::CSRF_TOKEN_MISMATCH, E_WARNING);
    }
}