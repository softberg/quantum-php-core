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
 * @since 2.9.7
 */

namespace Quantum\Hook\Exceptions;

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
        return new static(t('exception.duplicate_hook_name', $name), E_ERROR);
    }

    /**
     * @param string $name
     * @return HookException
     */
    public static function unregisteredHookName(string $name): HookException
    {
        return new static(t('exception.unregistered_hook_name', $name), E_WARNING);
    }
}