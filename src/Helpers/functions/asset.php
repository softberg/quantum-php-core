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

use Quantum\Libraries\Asset\AssetManager;


/**
 * Dumps the assets
 * @param string $type
 */
function assets(string $type)
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

/**
 * Asset url
 * @param string $filePath
 * @return string
 */
function asset(string $filePath): string
{
    return asset_url() . '/' . $filePath;
}

