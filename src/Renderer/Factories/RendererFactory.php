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

namespace Quantum\Renderer\Factories;

use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Renderer\Exceptions\RendererException;
use Quantum\Renderer\Adapters\HtmlAdapter;
use Quantum\Renderer\Adapters\TwigAdapter;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
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
    const ADAPTERS = [
        Renderer::HTML => HtmlAdapter::class,
        Renderer::TWIG => TwigAdapter::class,
    ];

    /**
     * @var Renderer|null
     */
    private static $instance = null;

    /**
     * @return Renderer
     * @throws RendererException
     * @throws BaseException
     * @throws DiException
     * @throws ConfigException
     * @throws ReflectionException
     */
    public static function get(): Renderer
    {
        if (self::$instance === null) {
            return self::$instance = self::createInstance();
        }

        return self::$instance;
    }

    /**
     * @return Renderer
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    private static function createInstance(): Renderer
    {
        if (!config()->has('view')) {
            config()->import(new Setup('config', 'view'));
        }

        $adapter = config()->get('view.current');

        $adapterClass = self::getAdapterClass($adapter);

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