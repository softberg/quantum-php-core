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

namespace Quantum\Hook\Exceptions;

use Quantum\Hook\Enums\ExceptionMessages;
use Quantum\App\Exceptions\BaseException;

/**
 * Class HookException
 * @package Quantum\Exceptions
 */
class HookException extends BaseException
{
    /**
     * @param string $name
     * @return HookException
     */
    public static function hookDuplicateName(string $name): HookException
    {
        return new static(_message(ExceptionMessages::DUPLICATE_HOOK_NAME, [$name]), E_ERROR);
    }

    /**
     * @param string $name
     * @return HookException
     */
    public static function unregisteredHookName(string $name): HookException
    {
        return new static(_message(ExceptionMessages::UNREGISTERED_HOOK_NAME, [$name]), E_WARNING);
    }
}