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
 * @since 1.0.0
 */

namespace Quantum\Hooks;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Exceptions\RouteException;
use Quantum\Http\Response;
use Twig_Loader_Filesystem;
use Twig_Environment;

/**
 * HookDefaults Class
 *
 * Default implementations
 *
 * @package Quantum
 * @subpackage Hooks
 * @category Hooks
 */
class HookDefaults implements HookInterface
{

    /**
     * Handle Headers
     *
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
     *
     * @throws RouteException
     */
    public static function pageNotFound()
    {
        throw new RouteException(ExceptionMessages::ROUTE_NOT_FOUND);
    }

    /**
     * Template renderer
     *
     * @param $data
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public static function templateRenderer($data)
    {
        $loader = new Twig_Loader_Filesystem(MODULES_DIR . DS . $data['currentModule'] . DS . 'Views');
        $twig = new Twig_Environment($loader, $data['configs']);

        $definedFunctions = get_defined_functions();

        $allDefinedFuncitons = array_merge($definedFunctions['internal'], $definedFunctions['user']);

        foreach ($allDefinedFuncitons as $function) {
            if (function_exists($function)) {
                $twig->addFunction(
                    new \Twig_Function(
                        $function, $function
                    )
                );
            }
        }

        return $twig->render($data['view'] . '.php', array_merge($data['params'], $data['sharedData']));
    }

    /**
     * Handling model not found
     *
     * @param string $modelName
     * @throws \Exception
     */
    public static function handleModel($modelName)
    {
        throw new \Exception(_message(ExceptionMessages::MODEL_NOT_FOUND, $modelName));
    }

}
