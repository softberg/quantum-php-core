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
 * @since 2.7.0
 */

namespace Quantum;

use Quantum\Exceptions\StopExecutionException;
use Quantum\Routes\ModuleLoader;
use Quantum\Libraries\Lang\Lang;
use Quantum\Environment\Server;
use Quantum\Debugger\Debugger;
use Quantum\Hooks\HookManager;
use Quantum\Mvc\MvcManager;
use Quantum\Routes\Router;
use Quantum\Loader\Loader;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Psr\Log\LogLevel;
use Quantum\Di\Di;

/**
 * Class Bootstrap
 * @package Quantum
 */
class Bootstrap
{

    /**
     * Boots the app
     * @param Loader $loader
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
    public static function run(Loader $loader)
    {
        try {
            $request = Di::get(Request::class);
            $response = Di::get(Response::class);

            $request->init(new Server);
            $response->init();

            $router = new Router($request, $response);

            ModuleLoader::loadModulesRoutes($router);

            Debugger::initStore();

            $loader->loadDir(base_dir() . DS . 'hooks');

            Debugger::addToStore(Debugger::HOOKS, LogLevel::INFO, HookManager::getRegistered());

            $router->findRoute();            

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
