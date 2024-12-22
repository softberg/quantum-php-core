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

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Exceptions\StopExecutionException;
use Quantum\Libraries\ResourceCache\ViewCache;
use Quantum\Libraries\Config\ConfigException;
use Quantum\Router\ModuleLoaderException;
use Quantum\Logger\LoggerManager;
use Quantum\Router\ModuleLoader;
use Quantum\Libraries\Lang\Lang;
use Quantum\Tracer\ErrorHandler;
use DebugBar\DebugBarException;
use Quantum\Environment\Server;
use Quantum\Debugger\Debugger;
use Quantum\Hooks\HookManager;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Error\LoaderError;
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
     * @throws DebugBarException
     * @throws Exceptions\ControllerException
     * @throws Exceptions\DiException
     * @throws Exceptions\MiddlewareException
     * @throws Exceptions\RouteException
     * @throws Exceptions\ViewException
     * @throws Libraries\Config\ConfigException
     * @throws Libraries\Csrf\CsrfException
     * @throws DatabaseException
     * @throws Libraries\Encryption\CryptorException
     * @throws Libraries\Lang\LangException
     * @throws Libraries\Session\SessionException
     * @throws LoaderError
     * @throws Logger\LoggerException
     * @throws ModuleLoaderException
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws SyntaxError
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

            ErrorHandler::getInstance()->setup(LoggerManager::getHandler());

            $debugger = Debugger::getInstance();
            $debugger->initStore();

            ModuleLoader::getInstance()->loadModulesRoutes();

            $viewCache = ViewCache::getInstance();

            if($viewCache->isEnabled()) {
                $viewCache->setup();
            }

            $router = new Router($request);
            $router->findRoute();

            $lang = Lang::getInstance();

            if($lang->isEnabled()) {
                $lang->load();
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
     * @throws ConfigException
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
