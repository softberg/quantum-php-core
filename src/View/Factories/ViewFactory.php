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

namespace Quantum\View\Factories;

use Quantum\Renderer\Factories\RendererFactory;
use Quantum\Libraries\ResourceCache\ViewCache;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Libraries\Asset\AssetManager;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use DebugBar\DebugBarException;
use Quantum\Debugger\Debugger;
use Quantum\View\QtView;
use ReflectionException;

/**
 * Class ViewFactory
 * @package Quantum\View
 * @mixin QtView
 */
class ViewFactory
{
    /**
     * Instance of QtView
     * @var QtView|null
     */
    private static $instance = null;

    /**
     * QtView instance
     * @return QtView
     * @throws DebugBarException
     * @throws DiException
     * @throws BaseException
     * @throws ConfigException
     * @throws ReflectionException
     */
    public static function get(): QtView
    {
        if (self::$instance === null) {
            self::$instance = new QtView(
                RendererFactory::get(),
                AssetManager::getInstance(),
                Debugger::getInstance(),
                ViewCache::getInstance()
            );
        }

        return self::$instance;
    }
}
