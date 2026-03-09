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

namespace Quantum\Session\Exceptions;

use Quantum\Session\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class SessionException
 * @package Quantum\Session
 */
class SessionException extends BaseException
{
    public static function sessionNotStarted(): self
    {
        return new self(
            ExceptionMessages::SESSION_NOT_STARTED,
            E_WARNING
        );
    }

    public static function sessionNotDestroyed(): self
    {
        return new self(
            ExceptionMessages::SESSION_NOT_DESTROYED,
            E_WARNING
        );
    }
}
