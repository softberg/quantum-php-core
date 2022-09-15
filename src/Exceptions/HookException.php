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
 * @since 2.8.0
 */

namespace Quantum\Exceptions;

/**
 * Class HookException
 * @package Quantum\Exceptions
 */
class HookException extends \Exception
{
    /**
     * @param string $name
     * @return \Quantum\Exceptions\HookException
     */
    public static function hookDuplicateName(string $name): HookException
    {
        return new static(t('exception.duplicate_hook_name', $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\HookException
     */
    public static function unregisteredHookName(string $name): HookException
    {
        return new static(t('exception.unregistered_hook_name', $name), E_WARNING);
    }
}
