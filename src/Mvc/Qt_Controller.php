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

namespace Quantum\Mvc;

use Quantum\Libraries\Debugger\Debugger;
use Quantum\Routes\RouteController;
use Quantum\Libraries\Session\Session;
use Quantum\Mvc\Qt_View;
use Quantum\Hooks\HookManager;

/**
 * Base Controller Class
 * 
 * Qt_Controller class is a base class that every controller should extend
 * 
 * @package Quantum
 * @subpackage MVC
 * @category MVC
 */
class Qt_Controller extends RouteController {

    /**
     * Reference of the Qt object
     * @var object 
     */
    private static $instance;

    /**
     * The data shared between layout and view
     * @var array 
     */
    private $sharedData = array();

    public function __construct() {
        
    }

    public function __before() {
        
    }

    public function __after() {
        
    }

    /**
     * Model Factory 
     * 
     * Deliver an object of request model
     * 
     * @param string $modelName
     * @return \Quantum\Mvc\modelClass
     */
    public function modelFactory($modelName) {
        $modelClass = "\\Modules\\" . RouteController::$currentRoute['module'] . "\\Models\\" . $modelName;

        if (class_exists($modelClass)) {
            return new $modelClass(RouteController::$currentRoute);
        } elseif (class_exists("\\Base\\models\\" . $modelName)) {
            $baseModelClass = "\\Base\\models\\" . $modelName;
            return new $baseModelClass(RouteController::$currentRoute);
        } else {
            HookManager::call("handleModel", $modelName);
        }
    }

    /**
     * Sets a layout
     * 
     * @param string $layout
     * @uses Qt_View::setLayout 
     * @return void
     */
    protected function setLayout($layout) {
        Qt_View::setLayout($layout);
    }

    /**
     * Share
     * 
     * Set the shared data, which becomes available in layout and view
     * 
     * @param mixed $data
     * @return void
     */
    protected function share($data) {
        foreach ($data as $key => $val) {
            $this->sharedData[$key] = $val;
        }
    }

    /**
     * Render
     * 
     * Renders the view 
     * 
     * @param string $view
     * @param array $params
     * @param bool $output
     * @return void
     */
    public function render($view, $params = array(), $output = false) {
        if (get_config('debug')) {
            $debugbarRenderer = Debugger::runDebuger($view);
            $this->share(['debugbarRenderer' => $debugbarRenderer]);
        }

        new Qt_View(RouteController::$currentRoute);
        Qt_View::render($view, $params, $output, $this->sharedData);
    }

    /**
     * Output
     * 
     * Outputs the view
     * 
     * @param string $view
     * @param array $params
     * @return void
     */
    public function output($view, $params = array()) {
        Qt_View::output($view, $params, $this->sharedData);
    }

    /**
     * GetInstance
     * 
     * Gets the Qt singleton
     * 
     * @return object
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}
