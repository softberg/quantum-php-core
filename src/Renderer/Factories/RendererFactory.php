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
 * @since 3.0.0
 */

namespace Quantum\Renderer\Factories;

use Quantum\Renderer\Exceptions\RendererException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Renderer\Adapters\HtmlAdapter;
use Quantum\Renderer\Adapters\TwigAdapter;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Renderer\Renderer;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class RendererFactory
 * @package Quantum\Renderer
 */
class RendererFactory
{
    /**
     * Supported adapters
     */
    public const ADAPTERS = [
        Renderer::HTML => HtmlAdapter::class,
        Renderer::TWIG => TwigAdapter::class,
    ];

    /**
     * @var array<string, Renderer>
     */
    private static $instances = [];

    /**
     * @param string|null $adapter
     * @return Renderer
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public static function get(?string $adapter = null): Renderer
    {
        if (!config()->has('view')) {
            config()->import(new Setup('config', 'view'));
        }

        $adapter ??= config()->get('view.default');

        $adapterClass = self::getAdapterClass($adapter);

        if (!isset(self::$instances[$adapter])) {
            self::$instances[$adapter] = self::createInstance($adapterClass, $adapter);
        }

        return self::$instances[$adapter];
    }

    /**
     * @param string $adapterClass
     * @param string $adapter
     * @return Renderer
     */
    private static function createInstance(string $adapterClass, string $adapter): Renderer
    {
        return new Renderer(new $adapterClass(config()->get('view.' . $adapter)));
    }

    /**
     * @param string $adapter
     * @return string
     * @throws BaseException
     */
    private static function getAdapterClass(string $adapter): string
    {
        if (!array_key_exists($adapter, self::ADAPTERS)) {
            throw RendererException::adapterNotSupported($adapter);
        }

        return self::ADAPTERS[$adapter];
    }
}
