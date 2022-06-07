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
 * @since 2.7.0
 */

namespace Quantum\Exceptions;

/**
 * Class HookException
 * @package Quantum\Exceptions
 */
class HookException extends \Exception
{
    /**
     * Duplicate hook implementer message
     */
    const DUPLICATE_HOOK_NAME = 'The Hook `{%1}` already registered';

    /**
     * Undeclared hook name message
     */
    const UNREGISTERED_HOOK_NAME = 'The Hook `{%1}` was not registered';

    /**
     * @param string $name
     * @return \Quantum\Exceptions\HookException
     */
    public static function hookDuplicateName(string $name): HookException
    {
        return new static(_message(self::DUPLICATE_HOOK_NAME, $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\HookException
     */
    public static function unregisteredHookName(string $name): HookException
    {
        return new static(_message(self::UNREGISTERED_HOOK_NAME, $name), E_WARNING);
    }
}
