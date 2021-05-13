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
 * @since 2.0.0
 */

namespace Quantum;

use Quantum\Exceptions\StopExecutionException;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Environment\Environment;
use Quantum\Libraries\Config\Config;
use Quantum\Routes\ModuleLoader;
use Quantum\Libraries\Lang\Lang;
use Quantum\Environment\Server;
use Quantum\Mvc\MvcManager;
use Quantum\Routes\Router;
use Quantum\Loader\Loader;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Quantum\Di\Di;

/**
 * Bootstrap Class
 * Bootstrap is the base class which is runner of all necessary components of framework.
 */
class Bootstrap
{

    /**
     * Boots the framework.
     * @throws \Exception|StopExecutionException
     */
    public static function run()
    {
        try {

            self::loadCoreFuncations();

            Di::loadDefinitions();

            $loader = Di::get(Loader::class);

            $fs = Di::get(FileSystem::class);

            Environment::getInstance()->load($loader);

            $request = Di::get(Request::class);
            $response = Di::get(Response::class);

            $request->init(new Server);
            $response->init();

            $router = new Router($request, $response);

            ModuleLoader::loadModulesRoutes($router, $fs);

            $router->findRoute();

            Config::getInstance()->load($loader);

            $loader->loadDir(base_dir() . DS . 'helpers');
            $loader->loadDir(base_dir() . DS . 'libraries');

            Lang::getInstance()
                    ->setLang($request->getSegment(config()->get('lang_segment')))
                    ->load($loader, $fs);

            MvcManager::runMvc($request, $response);

            $response->send();
            exit(0);
        } catch (StopExecutionException $e) {
            $response->send();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit(1);
        }
    }

    public static function loadCoreFuncations()
    {
        foreach (glob(HELPERS_DIR . DS . 'functions' . DS . '*.php') as $filename) {
            require_once $filename;
        }
    }

}
