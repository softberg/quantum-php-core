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

use Quantum\Libraries\Asset\Exceptions\AssetException;
use Quantum\Libraries\Lang\Exceptions\LangException;
use Quantum\Libraries\Asset\AssetManager;

/**
 * Gets the AssetFactory instance
 * @return AssetManager
 */
function asset(): AssetManager
{
    return AssetManager::getInstance();
}

/**
 * Dumps the assets
 * @param string $type
 * @return void
 * @throws AssetException
 * @throws LangException
 */
function assets(string $type): void
{
    AssetManager::getInstance()->dump(AssetManager::STORES[$type]);
}