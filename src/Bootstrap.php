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

use Quantum\Exceptions\StopExecutionException;
use Quantum\Router\ModuleLoader;
use Quantum\Libraries\Lang\Lang;
use Quantum\Environment\Server;
use Quantum\Debugger\Debugger;
use Quantum\Hooks\HookManager;
use Quantum\Mvc\MvcManager;
use Quantum\Router\Router;
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
            $request = Di::get(Request::class);
            $response = Di::get(Response::class);

            $request->init(new Server);
            $response->init();

            Debugger::initStore();

            ModuleLoader::loadModulesRoutes();

            $router = new Router($request, $response);
            $router->findRoute();

            if (config()->get('multilang')) {
                Lang::getInstance(config()->get(Lang::LANG_SEGMENT))->load();
            }

            Debugger::addToStore(Debugger::HOOKS, LogLevel::INFO, HookManager::getRegistered());

            MvcManager::handle($request, $response);

            stop();
        } catch (StopExecutionException $e) {
            $response->send();
        }
    }

}
