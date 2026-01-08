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
 * @since 3.0.0
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
    public static function tokenNotFound(): self
    {
        return new self(
            ExceptionMessages::CSRF_TOKEN_MISSING,
            E_WARNING
        );
    }

    /**
     * @return CsrfException
     */
    public static function tokenNotMatched(): self
    {
        return new self(
            ExceptionMessages::CSRF_TOKEN_MISMATCH,
            E_WARNING
        );
    }
}