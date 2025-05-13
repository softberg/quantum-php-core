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

use Quantum\Hook\HookManager;

/**
 * Gets the HookManager instance
 * @return HookManager
 */
function hook(): HookManager
{
    return HookManager::getInstance();
}