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

use Quantum\Routes\ModuleLoader;
use Quantum\Routes\Router;
use Quantum\Helpers\Helpers;
use Quantum\Libraries\Environment\Environment;
use Quantum\Libraries\Session\SessionManager;
use Quantum\Libraries\Config\Config;
use Quantum\Libraries\Libraries;
use Quantum\Mvc\MvcManager;
use Quantum\Mvc\Qt_Model;
use Quantum\Loader\Loader;

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
     * @throws Exception if one of these components fails: Router, Config, Helpers, Libraries, MVC MAnager.
     */
    public static function run()
    {
        try {
            $router = new Router();

            (new ModuleLoader())->loadModules($router);

            $router->findRoute();

            Environment::load();

            $loader = new Loader(Config::getSetup());
            Config::load($loader);

            Helpers::load();
            Libraries::load();

            $mvcManager = new MvcManager();
            $mvcManager->runMvc(Router::$currentRoute);
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

}
