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
 * @since 2.0.0
 */

namespace Quantum\Hooks;

use Quantum\Libraries\Database\Database;
use Quantum\Exceptions\ModelException;
use Quantum\Exceptions\RouteException;
use Quantum\Routes\RouteController;
use Twig\Loader\FilesystemLoader;
use Quantum\Debugger\Debugger;
use Quantum\Http\Response;
use Twig\TwigFunction;

/**
 * HookDefaults Class
 * Default implementations
 * 
 * @package Quantum
 * @category Hooks
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
     * @throws RouteException
     */
    public static function pageNotFound()
    {
        throw new RouteException(RouteException::ROUTE_NOT_FOUND);
    }

    /**
     * Template renderer
     * @param array $data
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public static function templateRenderer($data)
    {
        $loader = new FilesystemLoader(modules_dir() . DS . current_module() . DS . 'Views');
        $twig = new \Twig\Environment($loader, $data['configs']);

        $definedFunctions = get_defined_functions();

        $allDefinedFuncitons = array_merge($definedFunctions['internal'], $definedFunctions['user']);

        foreach ($allDefinedFuncitons as $function) {
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
    public static function updateDebuggerStore($data)
    {
        $currentRoute = RouteController::getCurrentRoute();

        $routeInfo  = [];

        array_walk($currentRoute, function ($value, $key) use (&$routeInfo) {
            $routeInfo[ucfirst($key)] = $value;
        });

        $routeInfo['View'] = current_module() . DS . 'Views' . DS . $data['view'];

        Debugger::addToStore(Debugger::ROUTES, 'info', $routeInfo);
        Debugger::addToStore(Debugger::QUERIES, 'info', Database::queryLog());
    }

}
