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

namespace Quantum\App\Adapters;

use Quantum\Libraries\Encryption\Exceptions\CryptorException;
use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Session\Exceptions\SessionException;
use Quantum\Libraries\Logger\Exceptions\LoggerException;
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Middleware\Exceptions\MiddlewareException;
use Quantum\Libraries\Csrf\Exceptions\CsrfException;
use Quantum\Libraries\Lang\Exceptions\LangException;
use Quantum\Router\Exceptions\ModuleLoaderException;
use Quantum\Environment\Exceptions\EnvException;
use Quantum\Exceptions\StopExecutionException;
use Quantum\Router\Exceptions\RouteException;
use Quantum\Exceptions\ControllerException;
use Quantum\App\Contracts\AppInterface;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Exceptions\ViewException;
use Quantum\App\Traits\WebAppTrait;
use DebugBar\DebugBarException;
use Quantum\Debugger\Debugger;
use Quantum\Hooks\HookManager;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Error\LoaderError;
use Quantum\Mvc\MvcManager;
use Quantum\Http\Response;
use Quantum\Http\Request;
use ReflectionException;
use Quantum\Di\Di;

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

/**
 * Class WebAppAdapter
 * @package Quantum\App
 */
class WebAppAdapter extends AppAdapter implements AppInterface
{

    use WebAppTrait;

    /**
     * @return int|null
     * @throws BaseException
     * @throws ConfigException
     * @throws ControllerException
     * @throws CryptorException
     * @throws CsrfException
     * @throws DatabaseException
     * @throws DebugBarException
     * @throws DiException
     * @throws EnvException
     * @throws LangException
     * @throws LoaderError
     * @throws LoggerException
     * @throws MiddlewareException
     * @throws ModuleLoaderException
     * @throws ReflectionException
     * @throws RouteException
     * @throws RuntimeError
     * @throws SessionException
     * @throws SyntaxError
     * @throws ViewException
     */
    public function start(): ?int
    {
        $request = Di::get(Request::class);
        $response = Di::get(Response::class);

        try {
            $this->loadEnvironment();
            $this->loadConfig();

            $this->initializeRequestResponse($request, $response);

            $this->loadLanguage();

            if ($request->isMethod('OPTIONS')) {
                stop();
            }

            $this->setupErrorHandler();
            $this->initializeDebugger();
            $this->loadModules();
            $this->setupViewCache();

            $this->initializeRouter($request);

            info(HookManager::getInstance()->getRegistered(), ['tab' => Debugger::HOOKS]);

            MvcManager::handle($request, $response);

            stop();
        } catch (StopExecutionException $exception) {
            self::handleCors($response);
            $response->send();

            return $exception->getCode();
        }
    }
}