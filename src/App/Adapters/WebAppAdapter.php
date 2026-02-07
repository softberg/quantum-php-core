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

namespace Quantum\App\Adapters;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Session\Exceptions\SessionException;
use Quantum\Middleware\Exceptions\MiddlewareException;
use Quantum\Libraries\Csrf\Exceptions\CsrfException;
use Quantum\Libraries\Lang\Exceptions\LangException;
use Quantum\App\Exceptions\StopExecutionException;
use Quantum\Environment\Exceptions\EnvException;
use Quantum\Module\Exceptions\ModuleException;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Router\Exceptions\RouteException;
use Quantum\Http\Exceptions\HttpException;
use Quantum\Middleware\MiddlewareManager;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\App\Traits\WebAppTrait;
use Quantum\Router\RouteCollection;
use Quantum\Router\RouteDispatcher;
use Quantum\Router\RouteBuilder;
use Quantum\Module\ModuleLoader;
use DebugBar\DebugBarException;
use Quantum\Router\RouteFinder;
use Quantum\Debugger\Debugger;
use Quantum\Hook\HookManager;
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
     * @throws ConfigException
     * @throws DiException
     * @throws EnvException
     * @throws ReflectionException
     */
    public function __construct()
    {
        parent::__construct();

        $this->loadEnvironment();
        $this->loadAppConfig();

        $this->request = Di::get(Request::class);
        $this->response = Di::get(Response::class);
    }

    /**
     * Starts the web app
     * @return int|null
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

            $this->setupErrorHandler();
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

            $this->loadLanguage();

            info(HookManager::getInstance()->getRegistered(), ['tab' => Debugger::HOOKS]);

            $middlewareManager = new MiddlewareManager($matchedRoute);

            [$this->request, $this->response] = $middlewareManager->applyMiddlewares(
                $this->request,
                $this->response
            );

            $viewCache = $this->setupViewCache();

            if ($viewCache->serveCachedView(route_uri(), $this->response)) {
                stop();
            }

            $dispatcher = new RouteDispatcher();
            $dispatcher->dispatch($matchedRoute, $this->request, $this->response);
            stop();
        } catch (StopExecutionException $exception) {
            $this->handleCors($this->response);
            $this->response->send();

            return $exception->getCode();
        }
    }
}
