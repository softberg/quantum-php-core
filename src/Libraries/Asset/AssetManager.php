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
 * @since 2.9.0
 */

namespace Quantum\Libraries\Asset;

use Quantum\Exceptions\AssetException;
use Quantum\Exceptions\LangException;

/**
 * Class AssetManager
 * @package Quantum\Libraries\Asset
 */
class AssetManager
{

    /**
     * CSS assets store
     */
    const CSS_STORE = 1;

    /**
     * JS assets store
     */
    const JS_STORE = 2;

    /**
     *  Asset store
     * @var Asset[]
     */
    private $store = [];

    /**
     * Published assets
     * @var array
     */
    private $published = [];

    /**
     * Asset instance
     * @var AssetManager|null
     */
    private static $instance = null;

    /**
     * AssetManager instance
     * @return AssetManager|null
     */
    public static function getInstance(): ?AssetManager
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Register assets
     * @param Asset[] $assets
     * @throws AssetException
     * @throws LangException
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
     * @throws LangException
     */
    public function registerAsset(Asset $asset)
    {
        if ($asset->getName()) {
            foreach ($this->store as $storedAsset) {
                if ($storedAsset->getName() == $asset->getName()) {
                    throw AssetException::nameInUse($asset->getName());
                }
            }
        }

        $this->store[] = $asset;
    }

    /**
     * Dumps the assets
     * @param int $type
     * @throws AssetException
     * @throws LangException
     */
    public function dump(int $type)
    {
        $this->publish();

        if (count($this->published[$type])) {
            foreach ($this->published[$type] as $asset) {
                echo $asset->tag();
            }
        }
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
     * Publishes assets
     * @throws AssetException
     * @throws LangException
     */
    private function publish()
    {
        if (empty($this->published)) {
            if (!empty($this->store)) {
                $this->setPriorityAssets();
                $this->setRegularAssets();
            }

            ksort($this->published[self::CSS_STORE]);
            ksort($this->published[self::JS_STORE]);
        }
    }

    /**
     * Sets assets with ordered position
     * @throws AssetException
     * @throws LangException
     */
    private function setPriorityAssets()
    {
        foreach ($this->store as $asset) {
            if ($asset->getPosition() != -1) {
                if (isset($this->published[$asset->getType()][$asset->getPosition()])) {
                    throw AssetException::positionInUse($asset->getPosition(), $asset->getPath());
                }

                $this->published[$asset->getType()][$asset->getPosition()] = $asset;
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
        if (isset($this->published[$asset->getType()][$index])) {
            $this->setPosition($asset, $index + 1);
        } else {
            $this->published[$asset->getType()][$index] = $asset;
        }
    }
}
