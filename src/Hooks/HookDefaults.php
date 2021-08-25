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
 * @since 2.5.0
 */

namespace Quantum\Hooks;

use Quantum\Libraries\Database\Database;
use Quantum\Exceptions\RouteException;
use Quantum\Routes\RouteController;
use Twig\Loader\FilesystemLoader;
use Quantum\Debugger\Debugger;
use Quantum\Http\Response;
use Twig\TwigFunction;

/**
 * Class HookDefaults
 * @package Quantum\Hooks
 */
class HookDefaults implements HookInterface
{

    /**
     * Handle Headers
     * Allows Cross domain requests
     */
    public static function handleHeaders()
    {
        Response::setHeader('Access-Control-Allow-Origin', '*');
        Response::setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
        Response::setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }

    /**
     * Page not found
     * @throws \Quantum\Exceptions\RouteException
     */
    public static function pageNotFound()
    {
        throw RouteException::notFound();
    }

    /**
     * Template renderer
     * @param array $data
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public static function templateRenderer(array $data): string
    {
        $loader = new FilesystemLoader(modules_dir() . DS . current_module() . DS . 'Views');
        $twig = new \Twig\Environment($loader, $data['configs']);

        $definedFunctions = get_defined_functions();

        $allDefinedFunctions = array_merge($definedFunctions['internal'], $definedFunctions['user']);

        foreach ($allDefinedFunctions as $function) {
            if (function_exists($function)) {
                $twig->addFunction(new TwigFunction($function, $function));
            }
        }

        return $twig->render($data['view'] . '.php', $data['params']);
    }

    /**
     * Updates debugger store
     * @param array $data
     */
    public static function updateDebuggerStore(array $data)
    {
        $currentRoute = RouteController::getCurrentRoute();
        $routeInfo  = [];

        array_walk($currentRoute, function ($value, $key) use (&$routeInfo) {
            $routeInfo[ucfirst($key)] = !is_array($value) ?: implode(',', $value);
        });

        $routeInfo['View'] = current_module() . DS . 'Views' . DS . $data['view'];

        Debugger::addToStore(Debugger::ROUTES, 'info', $routeInfo);
        Debugger::addToStore(Debugger::QUERIES, 'info', Database::queryLog());
    }

}
