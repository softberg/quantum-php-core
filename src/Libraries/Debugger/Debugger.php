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
 * @since 1.2.0
 */

namespace Quantum\Libraries\Debugger;

use DebugBar\DebugBar;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\StandardDebugBar;
use Quantum\Routes\ModuleLoader;
use Quantum\Routes\RouteController;
use ORM;

/**
 * Debugger class
 *
 * @package Quantum
 * @subpackage Libraries.Debugger
 * @category Libraries
 * @uses DebugBar
 */
class Debugger extends DebugBar
{

    /**
     * Debugbar instance
     * @var object
     */
    public static $debugbar;

    /**
     * Debugbar Renderer
     * @var object
     */
    public static $debugbarRenderer = null;

    /**
     * Queries
     * @var array
     */
    public static $queries;

    /**
     * Assets url
     * @var string
     */
    public static $assets_url;

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->addCollector(new PhpInfoCollector());
        $this->addCollector(new MessagesCollector());
        $this->addCollector(new RequestDataCollector());
        $this->addCollector(new MemoryCollector());
    }

    /**
     * Runs the debug bar
     *
     * @param string $view
     * @return object
     */
    public static function runDebuger($view = null)
    {
        self::$debugbar = new Debugger();
        self::$assets_url = base_url() . '/assets/DebugBar/Resources';
        self::addQueries();
        self::addMessages();
        self::addRoute($view);
        self::addMailLog();

        self::$debugbarRenderer = self::$debugbar->getJavascriptRenderer()
            ->setBaseUrl(self::$assets_url)
            ->addAssets(['custom_debugbar.css'], []);

        return self::$debugbarRenderer;
    }

    /**
     * Collects the queries
     *
     * @return void
     */
    private static function addQueries()
    {
        self::$debugbar->addCollector(new MessagesCollector('queries'));
        self::$queries = ORM::get_query_log();
        if (self::$queries) {
            foreach (self::$queries as $query) {
                self::$debugbar['queries']->info($query);
            }
        }
    }

    /**
     * Collects the routes
     *
     * @return void
     */
    private static function addRoute($view)
    {
        $uri = RouteController::$currentRoute['uri'];
        $method = RouteController::$currentRoute['method'];
        $module = RouteController::$currentRoute['module'];
        $current_controller = 'modules' . DS . $module . DS . RouteController::$currentRoute['controller'];
        $current_action = RouteController::$currentRoute['action'];
        $args = RouteController::$currentRoute['args'];

        $route = [
            'Route' => $uri,
            'Method' => $method,
            'Module' => $module,
            'Controller' => $current_controller,
            'Action' => $current_action,
            'View' => '',
            'Args' => $args,
        ];

        if ($view) {
            $route['View'] = 'modules/' . RouteController::$currentRoute['module'] . '/Views/' . $view;
        }

        self::$debugbar->addCollector(new MessagesCollector('routes'));
        self::$debugbar['routes']->info($route);
    }

    /**
     * Collects the messages
     *
     * @return void
     */
    private static function addMessages()
    {
        $out_data = session()->get('output');
        if ($out_data) {
            foreach ($out_data as $data) {
                self::$debugbar['messages']->debug($data);
            }
            session()->delete('output');
        }
    }

    /**
     * Collects the mails
     *
     * @return void
     */
    private static function addMailLog()
    {
        $mail_log = session()->get('mail_log');
        if ($mail_log) {
            self::$debugbar->addCollector(new MessagesCollector('mail'));
            $logs = explode('&', $mail_log);
            foreach ($logs as $log) {
                self::$debugbar['mail']->info($log);
            }
            session()->delete('mail_log');
        }
    }


}
