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

namespace Quantum\Libraries\Asset;

use Quantum\Exceptions\AssetException;

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
     * Asset storage
     * @var array[][]
     */
    private $storage = [];

    /**
     * Published assets
     * @var array
     */
    private $published = [];

    /**
     * Asset templates
     * @var string[]
     */
    private $templates = [
        self::CSS_STORE => '<link rel="stylesheet" type="text/css" href="{%1}">',
        self::JS_STORE => '<script src="{%1}"></script>',
    ];

    /**
     * Asset instance
     * @var \Quantum\Libraries\Asset\AssetManager|null
     */
    private static $instance = null;

    /**
     * AssetManager instance
     * @return \Quantum\Libraries\Asset\AssetManager|null
     */
    public static function getInstance(): ?AssetManager
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Registers assets
     * @param \Quantum\Libraries\Asset\Asset[] $assets
     */
    public function register(array $assets)
    {
        foreach ($assets as $asset) {
            $this->registerAsset($asset);
        }
    }

    /**
     * Dumps the assets
     * @param int $type
     * @throws \Quantum\Exceptions\AssetException
     */
    public function dump(int $type)
    {
        if (empty($this->published)) {
            $this->publish();
        }

        if (count($this->published[$type])) {
            foreach ($this->published[$type] as $path) {
                echo _message($this->templates[$type], $this->url($path)) . PHP_EOL;
            }
        }
    }

    /**
     * Asset url
     * @param string $path
     * @return string
     */
    public function url(string $path): string
    {
        return base_url() . '/assets/' . $path;
    }

    /**
     * Publishes assets
     * @throws \Quantum\Exceptions\AssetException
     */
    private function publish()
    {
        if (!empty($this->storage)) {
            foreach ($this->storage as $asset) {
                if ($asset->getPosition() != -1) {
                    if (isset($this->published[$asset->getType()][$asset->getPosition()])) {
                        throw AssetException::positionInUse($asset->getPosition(), $asset->getPath());
                    }

                    $this->published[$asset->getType()][$asset->getPosition()] = $asset->getPath();
                }
            }

            foreach ($this->storage as $asset) {
                if ($asset->getPosition() == -1) {
                    $this->setPosition($asset->getType(), 0, $asset->getPath());
                }
            }

            ksort($this->published[self::CSS_STORE]);
            ksort($this->published[self::JS_STORE]);
        }
    }

    /**
     * Registers an asset
     * @param \Quantum\Libraries\Asset\Asset $asset
     */
    private function registerAsset(Asset $asset)
    {
        $this->storage[] = $asset;
    }

    /**
     * Sets the Position
     * @param int $type
     * @param int $index
     * @param string $value
     */
    private function setPosition(int $type, int $index, string $value)
    {
        if (isset($this->published[$type][$index])) {
            $this->setPosition($type, $index + 1, $value);
        } else {
            $this->published[$type][$index] = $value;
        }
    }

}
