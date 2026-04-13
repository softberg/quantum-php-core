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

namespace Quantum\App\Adapters;

use Quantum\Middleware\Exceptions\MiddlewareException;
use Quantum\App\Exceptions\StopExecutionException;
use Quantum\Loader\Exceptions\LoaderException;
use Quantum\Module\Exceptions\ModuleException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Stages\SetupErrorHandlerStage;
use Quantum\Router\Exceptions\RouteException;
use Quantum\App\Stages\LoadEnvironmentStage;
use Quantum\Csrf\Exceptions\CsrfException;
use Quantum\Lang\Exceptions\LangException;
use Quantum\App\Stages\LoadAppConfigStage;
use Quantum\Middleware\MiddlewareManager;
use Quantum\App\Stages\LoadLanguageStage;
use Quantum\App\Exceptions\BaseException;
use Quantum\App\Stages\LoadHelpersStage;
use Quantum\App\Stages\InitHttpStage;
use Quantum\Di\Exceptions\DiException;
use Quantum\App\Traits\WebAppTrait;
use Quantum\Router\RouteCollection;
use Quantum\Router\RouteDispatcher;
use Quantum\Router\RouteBuilder;
use Quantum\Module\ModuleLoader;
use Quantum\Router\RouteFinder;
use Quantum\Debugger\Debugger;
use Quantum\App\Enums\AppType;
use Quantum\App\BootPipeline;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Class WebAppAdapter
 * @package Quantum\App
 */
class WebAppAdapter extends AppAdapter
{
    use WebAppTrait;

    public function __construct()
    {
        parent::__construct(AppType::WEB);

        $pipeline = new BootPipeline([
            new LoadHelpersStage(),
            new LoadEnvironmentStage(),
            new LoadAppConfigStage(),
            new SetupErrorHandlerStage(),
            new InitHttpStage(),
        ]);

        $pipeline->run($this->context);
    }

    /**
     * Starts the web app
     * @throws ModuleException|MiddlewareException|LangException|RouteException|CsrfException|ConfigException|DiException|BaseException|LoaderException|ReflectionException
     */
    public function start(): ?int
    {
        try {
            if (request()->isMethod('OPTIONS')) {
                stop();
            }

            $this->initializeDebugger();

            $moduleLoader = Di::get(ModuleLoader::class);

            $builder = new RouteBuilder();

            $collection = $builder->build(
                $moduleLoader->loadModulesRoutes(),
                $moduleLoader->getModuleConfigs()
            );

            Di::set(RouteCollection::class, $collection);

            $routeFinder = new RouteFinder($collection);

            $matchedRoute = $routeFinder->find(request());

            if ($matchedRoute === null) {
                page_not_found();
                stop();
            }

            request()->setMatchedRoute($matchedRoute);

            (new LoadLanguageStage())->process($this->context);

            $debugger = Di::get(Debugger::class);
            if ($debugger->isEnabled()) {
                $debugger->addToStoreCell(Debugger::HOOKS, 'info', hook()->getRegistered());
            }

            $middlewareManager = new MiddlewareManager($matchedRoute);

            [$request, $response] = $middlewareManager->applyMiddlewares(request(), response());

            $viewCache = $this->setupViewCache();

            if ($viewCache->serveCachedView(route_uri() ?? '', $response)) {
                stop();
            }

            $dispatcher = new RouteDispatcher();
            $dispatcher->dispatch($matchedRoute, $request);
            stop();
        } catch (StopExecutionException $exception) {
            $this->handleCors(response());
            response()->send();

            return $exception->getCode();
        }
    }
}
