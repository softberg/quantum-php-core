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
use Quantum\App\Exceptions\BaseException;
use Quantum\App\Stages\InitDebuggerStage;
use Quantum\Middleware\MiddlewareManager;
use Quantum\App\Stages\LoadHelpersStage;
use Quantum\App\Stages\InitHttpStage;
use Quantum\Di\Exceptions\DiException;
use Quantum\App\Traits\WebAppTrait;
use Quantum\Router\RouteDispatcher;
use Quantum\App\BootPipeline;
use Quantum\App\AppContext;
use ReflectionException;

/**
 * Class WebAppAdapter
 * @package Quantum\App
 */
class WebAppAdapter extends AppAdapter
{
    use WebAppTrait;

    public function __construct(AppContext $context)
    {
        parent::__construct($context);

        $pipeline = new BootPipeline([
            new LoadHelpersStage(),
            new LoadEnvironmentStage(),
            new LoadAppConfigStage(),
            new SetupErrorHandlerStage(),
            new InitHttpStage(),
            new InitDebuggerStage(),
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

            $this->loadModules();

            $matchedRoute = $this->resolveRoute();

            $this->loadLanguage();

            $this->logDebugInfo();

            [$request, $response] = (new MiddlewareManager($matchedRoute))->applyMiddlewares(request(), response());

            if ($this->setupViewCache()->serveCachedView(route_uri() ?? '', $response)) {
                stop();
            }

            (new RouteDispatcher())->dispatch($matchedRoute, $request);
            stop();
        } catch (StopExecutionException $exception) {
            $this->sendResponse();

            return $exception->getCode();
        }
    }
}
