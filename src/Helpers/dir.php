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
 * @since 2.6.0
 */

use Quantum\App;

/**
 * Gets base directory
 * @return string
 */
function base_dir(): string
{
    return App::getBaseDir();
}

/**
 * Gets the log directory
 * @return string
 */
function logs_dir(): string
{
    return base_dir() . DS . 'logs';
}

/**
 * Gets the framework directory
 * @return string
 */
function framework_dir(): string
{
    return base_dir() . DS . 'vendor' . DS . 'quantum' . DS . 'framework' . DS . 'src';
}

/**
 * Gets modules directory
 * @param string|null $moduleDir
 * @return string
 */
function modules_dir(string $moduleDir = null): string
{
    return $moduleDir ?? base_dir() . DS . 'modules';
}

/**
 * Gets public directory
 * @return string
 */
function public_dir(): string
{
    return base_dir() . DS . 'public';
}

/**
 * Gets uploads directory
 * @return string
 */
function uploads_dir(): string
{
    return public_dir() . DS . 'uploads';
}

/**
 * Gets assets directory
 * @return string
 */
function assets_dir(): string
{
    return public_dir() . DS . 'assets';
}

/**
 * Gets hooks directory
 * @return string
 */
function hooks_dir(): string
{
    return base_dir() . DS . 'hooks';
}



