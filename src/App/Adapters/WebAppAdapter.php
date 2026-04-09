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
use Quantum\Database\Exceptions\DatabaseException;
use Quantum\App\Exceptions\StopExecutionException;
use Quantum\Session\Exceptions\SessionException;
use Quantum\Module\Exceptions\ModuleException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Stages\SetupErrorHandlerStage;
use Quantum\Router\Exceptions\RouteException;
use Quantum\App\Stages\LoadEnvironmentStage;
use Quantum\Http\Exceptions\HttpException;
use Quantum\Csrf\Exceptions\CsrfException;
use Quantum\Lang\Exceptions\LangException;
use Quantum\App\Stages\LoadAppConfigStage;
use Quantum\Middleware\MiddlewareManager;
use Quantum\App\Stages\LoadLanguageStage;
use Quantum\App\Exceptions\BaseException;
use Quantum\App\Stages\LoadHelpersStage;
use Quantum\Di\Exceptions\DiException;
use Quantum\App\Traits\WebAppTrait;
use Quantum\Router\RouteCollection;
use Quantum\Router\RouteDispatcher;
use Quantum\Router\RouteBuilder;
use Quantum\Module\ModuleLoader;
use DebugBar\DebugBarException;
use Quantum\Router\RouteFinder;
use Quantum\Debugger\Debugger;
use Quantum\App\Enums\AppType;
use Quantum\App\BootPipeline;
use Quantum\Http\Response;
use Quantum\Http\Request;
use ReflectionException;
use Quantum\Di\Di;

/**
 * Class WebAppAdapter
 * @package Quantum\App
 */
class WebAppAdapter extends AppAdapter
{
    use WebAppTrait;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @throws BaseException
     * @throws DiException
     * @throws ReflectionException
     */
    public function __construct()
    {
        parent::__construct(AppType::WEB);

        $pipeline = new BootPipeline([
            new LoadHelpersStage(),
            new LoadEnvironmentStage(),
            new LoadAppConfigStage(),
        ]);

        $pipeline->run($this->context);

        $this->request = Di::get(Request::class);
        $this->response = Di::get(Response::class);
    }

    /**
     * Starts the web app
     * @throws BaseException
     * @throws ConfigException
     * @throws CsrfException
     * @throws DatabaseException
     * @throws DebugBarException
     * @throws DiException
     * @throws HttpException
     * @throws LangException
     * @throws ModuleException
     * @throws ReflectionException
     * @throws RouteException
     * @throws SessionException
     * @throws MiddlewareException
     */
    public function start(): ?int
    {
        try {
            $this->initializeRequestResponse($this->request, $this->response);

            if ($this->request->isMethod('OPTIONS')) {
                stop();
            }

            (new SetupErrorHandlerStage())->process($this->context);
            $this->initializeDebugger();

            $moduleLoader = ModuleLoader::getInstance();

            $builder = new RouteBuilder();

            $collection = $builder->build(
                $moduleLoader->loadModulesRoutes(),
                $moduleLoader->getModuleConfigs()
            );

            Di::set(RouteCollection::class, $collection);

            $routeFinder = new RouteFinder($collection);

            $matchedRoute = $routeFinder->find($this->request);

            if ($matchedRoute === null) {
                page_not_found();
                stop();
            }

            $this->request->setMatchedRoute($matchedRoute);

            (new LoadLanguageStage())->process($this->context);

            $debugger = Di::get(Debugger::class);
            if ($debugger->isEnabled()) {
                $debugger->addToStoreCell(Debugger::HOOKS, 'info', hook()->getRegistered());
            }

            $middlewareManager = new MiddlewareManager($matchedRoute);

            [$this->request, $this->response] = $middlewareManager->applyMiddlewares(
                $this->request,
                $this->response
            );

            $viewCache = $this->setupViewCache();

            if ($viewCache->serveCachedView(route_uri() ?? '', $this->response)) {
                stop();
            }

            $dispatcher = new RouteDispatcher();
            $dispatcher->dispatch($matchedRoute, $this->request);
            stop();
        } catch (StopExecutionException $exception) {
            $this->handleCors($this->response);
            $this->response->send();

            return $exception->getCode();
        }
    }
}
