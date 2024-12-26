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

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Encryption\CryptorException;
use Quantum\Libraries\Session\SessionException;
use Quantum\Libraries\Config\ConfigException;
use Quantum\Libraries\Csrf\CsrfException;
use Quantum\Libraries\Lang\LangException;
use Quantum\Router\ModuleLoaderException;
use Quantum\Libraries\Config\Config;
use Quantum\Environment\Environment;
use Quantum\Logger\LoggerException;
use DebugBar\DebugBarException;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Error\LoaderError;
use Quantum\Loader\Loader;
use Quantum\Loader\Setup;
use ReflectionException;
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
     * @return void
     * @throws ConfigException
     * @throws CryptorException
     * @throws CsrfException
     * @throws DatabaseException
     * @throws DebugBarException
     * @throws Exceptions\AppException
     * @throws Exceptions\ControllerException
     * @throws Exceptions\DiException
     * @throws Exceptions\EnvException
     * @throws Exceptions\MiddlewareException
     * @throws Exceptions\RouteException
     * @throws Exceptions\ViewException
     * @throws LangException
     * @throws LoaderError
     * @throws LoggerException
     * @throws ModuleLoaderException
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws SessionException
     * @throws SyntaxError
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

