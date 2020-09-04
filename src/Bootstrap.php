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

namespace Quantum;

use Quantum\Exceptions\StopExecutionException;
use Quantum\Environment\Environment;
use Quantum\Libraries\Config\Config;
use Quantum\Routes\ModuleLoader;
use Quantum\Libraries\Lang\Lang;
use Quantum\Environment\Server;
use Quantum\Http\HttpRequest;
use Quantum\Mvc\MvcManager;
use Quantum\Routes\Router;
use Quantum\Loader\Loader;
use Quantum\Http\Response;
use Quantum\Http\Request;

/**
 * Bootstrap Class
 *
 * Bootstrap is the base class which is runner of all necessary components of framework.
 */
class Bootstrap
{

    /**
     * Initializes the framework.
     *
     * This method does not accept parameters and does not return anything.
     * It runs the router, prepare the config values, helpers, libraries and MVC Manager
     *
     * @return void
     * @throws Exception if one of these components fails: Router, Config, Helpers, Libraries, MVC Manager.
     */
    public static function run()
    {
        try {
            $loader = new Loader();

            $loader->loadDir(HELPERS_DIR . DS . 'functions');

            Environment::getInstance()->load($loader);

            HttpRequest::init(new Server);

            $request = new Request();
            $response = new Response();

            $router = new Router($request, $response);

            (new ModuleLoader($router))->loadModulesRoutes();

            $router->findRoute();

            Config::getInstance()->load($loader);

            $loader->loadDir(base_dir() . DS . 'helpers');
            $loader->loadDir(base_dir() . DS . 'libraries');

            Lang::getInstance()->setLang($request->getSegment(config()->get('lang_segment')))->load($loader);

            try {
                (new MvcManager())->runMvc($request, $response);
            } catch (StopExecutionException $e) {}

            $response->send();
            exit(0);
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit(1);
        }
    }

}
