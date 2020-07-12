<?php

use Quantum\Libraries\Asset\AssetManager;

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

if (!function_exists('asset')) {

    /**
     * Asset url
     * @return string
     */
    function asset($filePath)
    {
        return asset_url() . '/' . $filePath;
    }

}