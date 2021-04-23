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
 * @since 2.3.0
 */

namespace Quantum\Exceptions;

/**
 * AuthException class
 *
 * @package Quantum
 * @category Exceptions
 */
class HookException extends \Exception
{
    /**
     * Duplicate hook implementer message
     */
    const DUPLICATE_HOOK_IMPLEMENTER = 'Duplicate Hook implementer was detected';

    /**
     * Undeclared hook name message
     */
    const UNDECLARED_HOOK_NAME = 'The Hook `{%1}` was not declared';
}
