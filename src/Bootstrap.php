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

use Quantum\Exceptions\StopExecutionException;
use Quantum\Router\ModuleLoaderException;
use Quantum\Libraries\Lang\Lang;
use Quantum\Router\ModuleLoader;
use Quantum\Environment\Server;
use Quantum\Debugger\Debugger;
use Quantum\Hooks\HookManager;
use Quantum\Mvc\MvcManager;
use Quantum\Router\Router;
use Quantum\Http\Response;
use Quantum\Http\Request;
use Quantum\Loader\Setup;
use ReflectionException;
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
     * @throws Exceptions\ConfigException
     * @throws Exceptions\ControllerException
     * @throws Exceptions\CryptorException
     * @throws Exceptions\CsrfException
     * @throws Exceptions\DiException
     * @throws Exceptions\LangException
     * @throws Exceptions\MiddlewareException
     * @throws ModuleLoaderException
     * @throws Exceptions\RouteException
     * @throws Exceptions\ViewException
     * @throws ReflectionException
     * @throws \DebugBar\DebugBarException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public static function run()
    {
        try {
            $request = Di::get(Request::class);
            $response = Di::get(Response::class);

            $request->init(Server::getInstance());
            $response->init();

            if ($request->getMethod() == 'OPTIONS') {
                stop();
            }

            $debugger = Debugger::getInstance();
            $debugger->initStore();

            ModuleLoader::getInstance()->loadModulesRoutes();

            $router = new Router($request, $response);
            $router->findRoute();

            if (config()->get('multilang')) {
                Lang::getInstance((int)config()->get(Lang::LANG_SEGMENT))->load();
            }

            $debugger->addToStoreCell(Debugger::HOOKS, LogLevel::INFO, HookManager::getRegistered());

            MvcManager::handle($request, $response);

            stop();
        } catch (StopExecutionException $e) {
            self::handleCors($response);
            $response->send();
        }
    }

    /**
     * Handles CORS
     * @param Response $response
     * @throws Exceptions\ConfigException
     * @throws Exceptions\DiException
     * @throws ReflectionException
     */
    private static function handleCors(Response $response)
    {
        if (!config()->has('cors')) {
            config()->import(new Setup('config', 'cors'));
        }

        foreach (config()->get('cors') as $key => $value) {
            $response->setHeader($key, $value);
        }
    }

}
