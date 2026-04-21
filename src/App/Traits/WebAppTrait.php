<?php

declare(strict_types=1);

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
use Quantum\Module\Exceptions\ModuleException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Loader\Exceptions\LoaderException;
use Quantum\Router\Exceptions\RouteException;
use Quantum\Lang\Exceptions\LangException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Lang\Factories\LangFactory;
use Quantum\Di\Exceptions\DiException;
use Quantum\ResourceCache\ViewCache;
use Quantum\Router\RouteCollection;
use Quantum\Router\RouteBuilder;
use Quantum\Module\ModuleLoader;
use Quantum\Router\MatchedRoute;
use Quantum\Router\RouteFinder;
use Quantum\Debugger\Debugger;
use Quantum\Http\Response;
use Quantum\Loader\Setup;
use ReflectionException;
use Quantum\Di\Di;
use Exception;

/**
 * Trait WebAppTrait
 * @package Quantum\App
 */
trait WebAppTrait
{
    /**
     * @throws ModuleException|RouteException|DiException|ReflectionException
     */
    private function loadModules(): void
    {
        if (!Di::isRegistered(ModuleLoader::class)) {
            Di::register(ModuleLoader::class);
        }

        $moduleLoader = Di::get(ModuleLoader::class);

        $collection = (new RouteBuilder())->build(
            $moduleLoader->loadModulesRoutes(),
            $moduleLoader->getModuleConfigs()
        );

        Di::set(RouteCollection::class, $collection);
    }

    /**
     * @return MatchedRoute
     * @throws RouteException|StopExecutionException|ConfigException|BaseException|DiException|ReflectionException
     */
    private function resolveRoute(): MatchedRoute
    {
        $routeFinder = new RouteFinder(Di::get(RouteCollection::class));

        $matchedRoute = $routeFinder->find(request());

        if ($matchedRoute === null) {
            page_not_found();
            stop();
        }

        request()->setMatchedRoute($matchedRoute);

        return $matchedRoute;
    }

    /**
     * @throws LangException|ConfigException|DiException|BaseException|ReflectionException
     */
    private function loadLanguage(): void
    {
        $lang = LangFactory::get();

        if ($lang->isEnabled()) {
            $lang->load();
        }
    }

    /**
     * @throws DiException|ReflectionException
     */
    private function logDebugInfo(): void
    {
        $debugbar = debugbar();

        if ($debugbar->isEnabled()) {
            $debugbar->addToStoreCell(Debugger::HOOKS, 'info', hook()->getRegistered());
        }
    }

    /**
     * @throws ConfigException|DiException|ReflectionException|LoaderException
     */
    private function setupViewCache(): ViewCache
    {
        if (!Di::isRegistered(ViewCache::class)) {
            Di::register(ViewCache::class);
        }

        $viewCache = Di::get(ViewCache::class);

        if ($viewCache->isEnabled()) {
            $viewCache->setup();
        }

        return $viewCache;
    }

    /**
     * @throws ConfigException|LoaderException|DiException|ReflectionException
     */
    private function handleCors(Response $response): void
    {
        if (!config()->has('cors')) {
            config()->import(new Setup('config', 'cors'));
        }

        foreach (config()->get('cors') as $key => $value) {
            $response->setHeader($key, (string) $value);
        }
    }

    /**
     * @throws ConfigException|LoaderException|DiException|ReflectionException|Exception
     */
    private function sendResponse(): void
    {
        $this->handleCors(response());
        response()->send();
    }
}
