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
 * @since 2.9.9
 */

namespace Quantum\App\Traits;

use Quantum\Libraries\Logger\Factories\LoggerFactory;
use Quantum\Libraries\Lang\Exceptions\LangException;
use Quantum\Libraries\Lang\Factories\LangFactory;
use Quantum\Environment\Exceptions\EnvException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Environment\Environment;
use Quantum\Tracer\ErrorHandler;
use Quantum\Loader\Loader;
use Quantum\Config\Config;
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

    protected function loadCoreDependencies()
    {
        $file = dirname(__DIR__) . DS . 'Config' . DS . 'dependencies.php';

        $coreDependencies = (is_file($file) && is_array($deps = require $file)) ? $deps : [];

        Di::registerDependencies($coreDependencies);
    }

    /**
     * Loads component helper functions
     * @throws DiException
     * @throws ReflectionException
     */
    protected function loadComponentHelperFunctions(): void
    {
        $loader = Di::get(Loader::class);

        $components = [
            'Environment',
            'Config',
            'Router',
            'Model',
            'Hook',
            'Http',
            'View',
            'App',
        ];

        foreach ($components as $component) {
            $componentHelperPath = dirname(__DIR__, 2) . DS . $component . DS . 'Helpers';
            if (is_dir($componentHelperPath)) {
                $loader->loadDir($componentHelperPath);
            }
        }
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
     * Loads module helper functions
     * @throws DiException
     * @throws ReflectionException
     */
    protected function loadModuleHelperFunctions(): void
    {
        $loader = Di::get(Loader::class);
        $loader->loadDir(App::getBaseDir() . DS . 'modules' . DS . '*' . DS . 'helpers');
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
        Environment::getInstance()->load(new Setup('config', 'env'));
    }

    /**
     * @return void
     * @throws DiException
     * @throws ReflectionException
     * @throws ConfigException
     */
    protected function loadAppConfig()
    {
        if (!config()->has('app')) {
            Config::getInstance()->import(new Setup('config', 'app'));
        }
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
        $lang = LangFactory::get();

        if ($lang->isEnabled()) {
            $lang->load();
        }
    }
}