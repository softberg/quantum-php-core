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
 * @since 2.8.0
 */

namespace Quantum;

use Quantum\Environment\Environment;
use Quantum\Libraries\Config\Config;
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
     * @throws Exceptions\CsrfException
     * @throws Exceptions\DatabaseException
     * @throws Exceptions\DiException
     * @throws Exceptions\EnvException
     * @throws Exceptions\HookException
     * @throws Exceptions\LangException
     * @throws Exceptions\MiddlewareException
     * @throws Exceptions\ModuleLoaderException
     * @throws Exceptions\RouteException
     * @throws Exceptions\SessionException
     * @throws Exceptions\ViewException
     * @throws \ErrorException
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

        ErrorHandler::setup();

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

