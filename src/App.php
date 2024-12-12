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

namespace Quantum;

use Quantum\Router\ModuleLoaderException;
use Quantum\Environment\Environment;
use Quantum\Libraries\Config\Config;
use Quantum\Logger\LoggerException;
use Quantum\Logger\LoggerManager;
use Quantum\Tracer\ErrorHandler;
use Quantum\Loader\Loader;
use Quantum\Loader\Setup;
use Quantum\Di\Di;

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

/**
 * Class App
 * @package Quantum
 */
class App
{

    /**
     * @var string
     */
    public static $baseDir = __DIR__;

    /**
     * Starts the app
     * @param string $baseDir
     * @throws Exceptions\ConfigException
     * @throws Exceptions\ControllerException
     * @throws Exceptions\DiException
     * @throws Exceptions\EnvException
     * @throws Exceptions\MiddlewareException
     * @throws ModuleLoaderException
     * @throws Exceptions\RouteException
     * @throws Exceptions\ViewException
     * @throws LoggerException
     * @throws \ReflectionException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public static function start(string $baseDir)
    {
        self::loadCoreFunctions($baseDir . DS . 'vendor' . DS . 'quantum' . DS . 'framework' . DS . 'src' . DS . 'Helpers');

        self::setBaseDir($baseDir);

        Di::loadDefinitions();

        $loader = Di::get(Loader::class);

        $loader->loadDir(base_dir() . DS . 'helpers');
        $loader->loadDir(base_dir() . DS . 'libraries');
        $loader->loadDir(base_dir() . DS . 'hooks');

        Environment::getInstance()->load(new Setup('config', 'env'));

        Config::getInstance()->load(new Setup('config', 'config'));

        ErrorHandler::getInstance()->setup(LoggerManager::getHandler());

        Bootstrap::run();
    }

    /**
     * Loads the core functions
     * @param string $path
     */
    public static function loadCoreFunctions(string $path)
    {
        foreach (glob($path . DS . '*.php') as $filename) {
            require_once $filename;
        }
    }

    /**
     * Sets the app base directory
     * @param string $baseDir
     */
    public static function setBaseDir(string $baseDir)
    {
        self::$baseDir = $baseDir;
    }

    /**
     * Gets the app base directory
     * @return string
     */
    public static function getBaseDir(): string
    {
        return self::$baseDir;
    }

}

