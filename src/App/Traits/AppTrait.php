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

namespace Quantum\App\Traits;

use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Logger\Factories\LoggerFactory;
use Quantum\Libraries\Lang\Exceptions\LangException;
use Quantum\Environment\Exceptions\EnvException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Environment\Environment;
use Quantum\Libraries\Config\Config;
use Quantum\Tracer\ErrorHandler;
use Quantum\Libraries\Lang\Lang;
use Quantum\Loader\Loader;
use Quantum\Loader\Setup;
use ReflectionException;
use Quantum\App\App;
use Quantum\Di\Di;

/**
 * Class AppTrait
 * @package Quantum\App
 */
trait AppTrait
{

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

    /**
     * Loads the core helper functions
     * @throws DiException
     * @throws ReflectionException
     */
    protected function loadCoreHelperFunctions()
    {
        $loader = Di::get(Loader::class);
        $loader->loadDir(dirname(__DIR__, 2) . DS . 'Helpers');
    }

    /**
     * Loads library helper functions
     * @throws DiException
     * @throws ReflectionException
     */
    protected function loadLibraryHelperFunctions()
    {
        $loader = Di::get(Loader::class);
        $loader->loadDir(dirname(__DIR__, 2) . DS . 'Libraries' . DS . '*' . DS . 'Helpers');
    }

    /**
     * Loads app helper functions
     * @throws DiException
     * @throws ReflectionException
     */
    protected function loadAppHelperFunctions(): void
    {
        $loader = Di::get(Loader::class);
        $loader->loadDir(App::getBaseDir() . DS . 'helpers');
    }

    /**
     * @return void
     * @throws DiException
     * @throws ReflectionException
     * @throws EnvException
     * @throws BaseException
     */
    protected function loadEnvironment()
    {
        Environment::getInstance()->setup(new Setup('config', 'env'));
        Environment::getInstance()->load(new Setup('config', 'env'));
    }

    /**
     * @return void
     * @throws DiException
     * @throws ReflectionException
     * @throws ConfigException
     */
    protected function loadConfig()
    {
        Config::getInstance()->load(new Setup('config', 'config'));
    }

    /**
     * @return void
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    protected function setupErrorHandler()
    {
        ErrorHandler::getInstance()->setup(LoggerFactory::get());
    }

    /**
     * @return void
     * @throws BaseException
     * @throws DiException
     * @throws ReflectionException
     * @throws LangException
     */
    protected function loadLanguage()
    {
        $lang = Lang::getInstance();

        if ($lang->isEnabled()) {
            $lang->load();
        }
    }
}