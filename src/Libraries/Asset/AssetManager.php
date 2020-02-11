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
 * @since 1.9.9
 */

namespace Quantum\Libraries\Asset;

/**
 * Class AssetManager
 *
 * @package Quantum\Libraries\Asset
 */
class AssetManager
{

    /**
     * CSS Assets
     * @var array
     */
    private static $cssAssetStore = [
        'ordered' => [],
        'unordered' => [],
    ];

    /**
     * JS Assets
     * @var array 
     */
    private static $jsAssetStore = [
        'ordered' => [],
        'unordered' => [],
    ];

    /**
     * Register CSS
     * 
     * @param array $cssAssets
     */
    public static function registerCSS($cssAssets = [])
    {
        self::register($cssAssets, self::$cssAssetStore);
    }

    /**
     * Register JS
     * 
     * @param array $jsAssets
     */
    public static function registerJS($jsAssets = [])
    {
        self::register($jsAssets, self::$jsAssetStore);
    }

    /**
     * Publish CSS
     * 
     * @return array
     */
    public static function publishCSS()
    {
        return self::publish(self::$cssAssetStore);
    }

    /**
     * Publish JS
     * @return array
     */
    public static function publishJS()
    {
        return self::publish(self::$jsAssetStore);
    }

    /**
     * Register 
     * 
     * @param array $assets
     * @param array $assetStore
     */
    private static function register($assets, &$assetStore)
    {
        foreach ($assets as $asset) {
            if (is_array($asset)) {
                $assetStore['ordered'][$asset[1]] = $asset[0];
            } else {
                $assetStore['unordered'][] = $asset;
            }
        }
    }

    /**
     * Publish 
     * @param array $assets
     * @return array
     */
    private static function publish($assets)
    {
        foreach ($assets['unordered'] as $key => $value) {
            if (isset($assets['ordered'][$key])) {
                self::setPosition($assets['ordered'], $key + 1, $value);
            } else {
                $assets['ordered'][$key] = $value;
            }
        }
        ksort($assets['ordered']);

        return $assets['ordered'];
    }

    /**
     * Set Position
     * 
     * @param arraye $arr
     * @param int $key
     * @param mixed $value
     */
    private static function setPosition(&$arr, $key, $value)
    {
        if (isset($arr[$key])) {
            self::setPosition($arr, $key + 1, $value);
        } else {
            $arr[$key] = $value;
        }
    }

}
