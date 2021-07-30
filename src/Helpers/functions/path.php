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
 * @since 2.5.0
 */

/**
 * Gets base directory
 * @return string
 */
function base_dir(): string
{
    return BASE_DIR;
}

/**
 * Gets modules directory
 * @param string|null $moduleDir
 * @return string
 */
function modules_dir(string $moduleDir = null): string
{
    return $moduleDir ?? MODULES_DIR;
}

/**
 * Gets public directory
 * @return string
 */
function public_dir(): string
{
    return PUBLIC_DIR;
}

/**
 * Gets uploads directory
 * @return string
 */
function uploads_dir(): string
{
    return UPLOADS_DIR;
}

/**
 * Gets assets directory
 * @return string
 */
function assets_dir(): string
{
    return ASSETS_DIR;
}


