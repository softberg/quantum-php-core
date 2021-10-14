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

namespace Quantum;

use Quantum\Exceptions\StopExecutionException;
use Quantum\Libraries\Database\Database;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Environment\Environment;
use Quantum\Libraries\Config\Config;
use Quantum\Routes\ModuleLoader;
use Quantum\Libraries\Lang\Lang;
use Quantum\Environment\Server;
use Quantum\Debugger\Debugger;
use Quantum\Mvc\MvcManager;
use Quantum\Routes\Router;
use Quantum\Loader\Loader;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Quantum\Loader\Setup;
use Quantum\Di\Di;

/**
 * Class Bootstrap
 * @package Quantum
 */
class Bootstrap
{

    /**
     * Boots the app
     * @throws \Quantum\Exceptions\ConfigException
     * @throws \Quantum\Exceptions\ControllerException
     * @throws \Quantum\Exceptions\CsrfException
     * @throws \Quantum\Exceptions\DatabaseException
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\EnvException
     * @throws \Quantum\Exceptions\LangException
     * @throws \Quantum\Exceptions\MiddlewareException
     * @throws \Quantum\Exceptions\ModuleLoaderException
     * @throws \Quantum\Exceptions\RouteException
     * @throws \Quantum\Exceptions\SessionException
     * @throws \ReflectionException
     */
    public static function run()
    {
        try {

            $loader = Di::get(Loader::class);

            $fs = Di::get(FileSystem::class);

            Debugger::initStore();

            Environment::getInstance()->load(new Setup('config', 'env'));

            Config::getInstance()->load(new Setup('config', 'config'));

            $request = Di::get(Request::class);
            $response = Di::get(Response::class);

            $request->init(new Server);
            $response->init();

            $router = new Router($request, $response);

            ModuleLoader::loadModulesRoutes($router, $fs);

            $router->findRoute();

            $loader->loadDir(base_dir() . DS . 'helpers');
            $loader->loadDir(base_dir() . DS . 'libraries');

            if (config()->has('langs')) {
                Lang::getInstance()
                    ->setLang($request->getSegment(config()->get('lang_segment')))
                    ->load();
            }

            MvcManager::handle($request, $response);

            stop();
        } catch (StopExecutionException $e) {
            $response->send();
        }

    }


}
