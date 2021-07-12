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
 * @since 2.0.0
 */

if (!function_exists('base_dir')) {

    /**
     * Gets base directory
     * @return string
     */
    function base_dir(): string
    {
        return BASE_DIR;
    }

}

if (!function_exists('modules_dir')) {

    /**
     * Gets modules directory
     * @param string|null $moduleDir
     * @return string
     */
    function modules_dir(string $moduleDir = null): string
    {
        return $moduleDir ?? MODULES_DIR;
    }

}

if (!function_exists('public_dir')) {

    /**
     * Gets public directory
     * @return string
     */
    function public_dir(): string
    {
        return PUBLIC_DIR;
    }

}

if (!function_exists('uploads_dir')) {

    /**
     * Gets uploads directory
     * @return string
     */
    function uploads_dir(): string
    {
        return UPLOADS_DIR;
    }

}

if (!function_exists('assets_dir')) {

    /**
     * Gets assets directory
     * @return string
     */
    function assets_dir(): string
    {
        return ASSETS_DIR;
    }

}

if (!function_exists('asset')) {

    /**
     * Asset url
     * @param string $filePath
     * @return string
     */
    function asset(string $filePath): string
    {
        return assets_dir() . DS . $filePath;
    }

}

