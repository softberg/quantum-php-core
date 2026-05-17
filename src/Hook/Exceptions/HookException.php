<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman@quantumphp.io>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link https://quantumphp.io/
 * @since 3.0.0
 */

namespace Quantum\Hook\Exceptions;

use Quantum\Hook\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class HookException
 * @package Quantum\Exceptions
 */
class HookException extends BaseException
{
    public static function hookDuplicateName(string $name): self
    {
        return new self(
            _message(ExceptionMessages::DUPLICATE_HOOK_NAME, [$name]),
            E_ERROR
        );
    }

    public static function unregisteredHookName(string $name): self
    {
        return new self(
            _message(ExceptionMessages::UNREGISTERED_HOOK_NAME, [$name]),
            E_WARNING
        );
    }
}
