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
 * @since 2.9.7
 */

namespace Quantum\App\Traits;

use Quantum\App\Exceptions\StopExecutionException;
use Quantum\Libraries\ResourceCache\ViewCache;
use Quantum\Module\Exceptions\ModuleException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Router\Exceptions\RouteException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Module\ModuleLoader;
use DebugBar\DebugBarException;
use Quantum\Environment\Server;
use Quantum\Debugger\Debugger;
use Quantum\Http\Response;
use Quantum\Router\Router;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Trait WebAppTrait
 * @package Quantum\App
 */
trait WebAppTrait
{

    /**
     * @param $request
     * @param $response
     * @return void
     */
    private function initializeRequestResponse($request, $response)
    {
        $request->init(Server::getInstance());
        $response->init();
    }

    /**
     * @return void
     * @throws DebugBarException
     */
    private function initializeDebugger()
    {
        $debugger = Debugger::getInstance();
        $debugger->initStore();
    }

    /**
     * @return void
     * @throws ModuleException
     * @throws RouteException
     */
    private function loadModulesRoutes()
    {
        $modulesRoutes = ModuleLoader::getInstance()->loadModulesRoutes();
        Router::setRoutes($modulesRoutes);
    }

    /**
     * @return ViewCache
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    private function setupViewCache(): ViewCache
    {
        $viewCache = ViewCache::getInstance();

        if ($viewCache->isEnabled()) {
            $viewCache->setup();
        }

        return $viewCache;
    }

    /**
     * @param $request
     * @return void
     * @throws BaseException
     * @throws ConfigException
     * @throws DebugBarException
     * @throws DiException
     * @throws ReflectionException
     * @throws RouteException
     * @throws StopExecutionException
     */
    private function initializeRouter($request)
    {
        $router = new Router($request);
        $router->findRoute();
    }

    /**
     * @param Response $response
     * @return void
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    private function handleCors(Response $response)
    {
        if (!config()->has('cors')) {
            config()->import(new Setup('config', 'cors'));
        }

        foreach (config()->get('cors') as $key => $value) {
            $response->setHeader($key, $value);
        }
    }
}