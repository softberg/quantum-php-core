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

use Quantum\Debugger\Debugger;
use Quantum\Di\Di;

/**
 * Gets the Debugger instance from DI
 */
function debugbar(): Debugger
{
    if (!Di::isRegistered(Debugger::class)) {
        Di::register(Debugger::class);
    }

    return Di::get(Debugger::class);
}
