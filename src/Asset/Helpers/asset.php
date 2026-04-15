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

use Quantum\Asset\Exceptions\AssetException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Asset\AssetManager;
use Quantum\Di\Di;

/**
 * Gets the AssetManager instance
 * @throws DiException|ReflectionException
 */
function asset(): AssetManager
{
    if (!Di::isRegistered(AssetManager::class)) {
        Di::register(AssetManager::class);
    }

    return Di::get(AssetManager::class);
}

/**
 * Dumps the assets
 * @throws AssetException|DiException|ReflectionException
 */
function assets(string $type): void
{
    asset()->dump(AssetManager::STORES[$type]);
}
