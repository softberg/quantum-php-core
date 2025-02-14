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
 * @since 2.9.5
 */

namespace Quantum\Libraries\Asset;

use Quantum\Libraries\Asset\Exceptions\AssetException;
use Quantum\Libraries\Lang\Exceptions\LangException;

/**
 * Class AssetFactory
 * @package Quantum\Libraries\Asset
 */
class AssetManager
{

    /**
     * Assets store types
     */
    const STORES = [
        'css' => Asset::CSS,
        'js' => Asset::JS,
    ];

    /**
     *  Asset store
     * @var Asset[]
     */
    private $store = [];

    /**
     * Published assets
     * @var array[]
     */
    private $published = [];

    /**
     * Asset instance
     * @var AssetManager|null
     */
    private static $instance = null;

    private function __construct()
    {
        foreach (self::STORES as $type) {
            $this->published[$type] = [];
        }
    }

    /**
     * AssetManager instance
     * @return AssetManager
     */
    public static function getInstance(): AssetManager
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Gets the asset by name
     * @param string $name
     * @return Asset|null
     */
    public function get(string $name): ?Asset
    {
        foreach ($this->store as $asset) {
            if ($asset->getName() == $name) {
                return $asset;
            }
        }

        return null;
    }

    /**
     * Asset url
     * @param string $path
     * @return string
     */
    public function url(string $path): string
    {
        if (!parse_url($path, PHP_URL_HOST)) {
            return base_url() . '/assets/' . $path;
        }

        return $path;
    }

    /**
     * Register assets
     * @param Asset[] $assets
     * @throws AssetException
     */
    public function register(array $assets)
    {
        foreach ($assets as $asset) {
            $this->registerAsset($asset);
        }
    }

    /**
     * Register single asset
     * @param Asset $asset
     * @throws AssetException
     */
    public function registerAsset(Asset $asset)
    {
        if ($asset->getName() && $this->get($asset->getName())) {
            throw AssetException::nameInUse($asset->getName());
        }

        $this->store[] = $asset;
    }

    /**
     * @return void
     */
    public function flush()
    {
        $this->store = [];
        $this->published = [];
    }

    /**
     * Dumps the assets
     * @param int $type
     * @throws AssetException
     * @throws LangException
     */
    public function dump(int $type): void
    {
        if (empty($this->published[$type])) {
            $this->publish();
        }

        if ($this->published && count($this->published[$type])) {
            foreach ($this->published[$type] as $asset) {
                echo $asset->tag();
            }
        }
    }

    /**
     * Publishes assets
     * @throws AssetException
     */
    private function publish()
    {
        if (!empty($this->store)) {
            $this->setPriorityAssets();
            $this->setRegularAssets();

            ksort($this->published[Asset::CSS]);
            ksort($this->published[Asset::JS]);
        }
    }

    /**
     * Sets assets with ordered position
     * @throws AssetException
     */
    private function setPriorityAssets()
    {
        foreach ($this->store as $asset) {
            $position = $asset->getPosition();
            $type = $asset->getType();

            if ($position != -1) {
                if (isset($this->published[$type][$position])) {
                    throw AssetException::positionInUse($position, $asset->getPath());
                }

                $this->published[$type][$position] = $asset;
            }
        }
    }

    /**
     * Sets assets without ordered position
     */
    private function setRegularAssets()
    {
        foreach ($this->store as $asset) {
            if ($asset->getPosition() == -1) {
                $this->setPosition($asset, 0);
            }
        }
    }

    /**
     * Sets the Position
     * @param Asset $asset
     * @param int $index
     */
    private function setPosition(Asset $asset, int $index)
    {
        $type = $asset->getType();

        if (isset($this->published[$type][$index])) {
            $this->setPosition($asset, $index + 1);
        } else {
            $this->published[$type][$index] = $asset;
        }
    }
}