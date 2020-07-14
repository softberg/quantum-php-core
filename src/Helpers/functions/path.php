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

use Quantum\Libraries\Asset\AssetManager;

if (!function_exists('base_dir')) {

    /**
     * Gets base directory
     * @return string
     */
    function base_dir()
    {
        return BASE_DIR;
    }

}

if (!function_exists('modules_dir')) {

    /**
     * Gets modules directory
     * @return string
     */
    function modules_dir($moduleDir = null)
    {
        return $moduleDir ?? MODULES_DIR;
    }

}

if (!function_exists('public_dir')) {

    /**
     * Gets public directory
     * @return string
     */
    function public_dir()
    {
        return PUBLIC_DIR;
    }

}

if (!function_exists('uploads_dir')) {

    /**
     * Gets uploads directory
     * @return string
     */
    function uploads_dir()
    {
        return UPLOADS_DIR;
    }

}

if (!function_exists('assets_dir')) {

    /**
     * Gets assets directory
     * @return string
     */
    function assets_dir()
    {
        return ASSETS_DIR;
    }

}

if (!function_exists('asset')) {

    /**
     * Asset url
     * @return string
     */
    function asset($filePath)
    {
        return assets_dir() . DS . $filePath;
    }

}

if (!function_exists('assets')) {

    /**
     * Assets
     * @return string
     */
    function assets($type)
    {
        switch ($type) {
            case 'css':
                $cssAssets = AssetManager::publishCSS();

                if (count($cssAssets)) {
                    foreach ($cssAssets as $cssAsset) {
                        echo '<link rel="stylesheet" type="text/css" href="' . asset($cssAsset) . '">' . PHP_EOL;
                    }
                }
                break;
            case 'js':
                $jsAssets = AssetManager::publishJS();

                if (count($jsAssets)) {
                    foreach ($jsAssets as $jsAsset) {
                        echo '<script src="' . asset($jsAsset) . '"></script>' . PHP_EOL;
                    }
                }
                break;
        }
    }

}
