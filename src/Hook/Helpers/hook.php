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
 * @since 3.0.0
 */

use Quantum\Di\Exceptions\DiException;
use Quantum\Hook\HookManager;
use Quantum\Di\Di;

/**
 * Gets the HookManager instance
 * @throws DiException|ReflectionException
 */
function hook(): HookManager
{
    if (!Di::isRegistered(HookManager::class)) {
        Di::register(HookManager::class);
    }

    return Di::get(HookManager::class);
}
