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
 * @since 3.0.0
 */

namespace Quantum\App\Traits;

use Quantum\App\Exceptions\StopExecutionException;
use Quantum\Libraries\ResourceCache\ViewCache;
use Quantum\Module\Exceptions\ModuleException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Router\Exceptions\RouteException;
use Quantum\Http\Exceptions\HttpException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Module\ModuleLoader;
use DebugBar\DebugBarException;
use Quantum\Environment\Server;
use Quantum\Debugger\Debugger;
use Quantum\Http\Response;
use Quantum\Router\Router;
use Quantum\Http\Request;
use Quantum\Loader\Setup;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Trait WebAppTrait
 * @package Quantum\App
 */
trait WebAppTrait
{
    /**
     * @param Request $request
     * @param Response $response
     * @throws BaseException
     * @throws DiException
     * @throws ReflectionException
     * @throws HttpException
     */
    private function initializeRequestResponse(Request $request, Response $response)
    {
        $request->init(Server::getInstance());
        $response->init();
    }

    /**
     * @throws DebugBarException
     */
    private function initializeDebugger()
    {
        $debugger = Debugger::getInstance();
        $debugger->initStore();
    }

    /**
     * Load modules
     * @throws ModuleException
     * @throws RouteException
     */
    private function loadModules()
    {
        $moduleLoader = ModuleLoader::getInstance();

        $modulesDependencies = $moduleLoader->loadModulesDependencies();
        Di::registerDependencies($modulesDependencies);

        $modulesRoutes = $moduleLoader->loadModulesRoutes();
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
