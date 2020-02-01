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
     * 
     * @var object
     */
    private $debugbar;

    /**
     * Queries
     * 
     * @var array
     */
    private $queries = [];

    /**
     * Assets url
     * 
     * @var string
     */
    private $assetsUrl = '/assets/DebugBar/Resources';

    /**
     * Custom CSS
     * 
     * @var string 
     */
    private $customCss = 'custom_debugbar.css';

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
    public function run($view = null)
    {
        $this->debugbar = new Debugger();

        $this->tabMessages();
        $this->tabQueries();
        $this->tabRoutes($view);
        $this->tabMailLog();

        return $debugbarRenderer = $this->debugbar
                ->getJavascriptRenderer()
                ->setBaseUrl(base_url() . $this->assetsUrl)
                ->addAssets([$this->customCss], []);
    }

    /**
     * Tab Messages
     * 
     * Output debug messages in Messages tab
     *
     * @return void
     */
    private function tabMessages()
    {
        $outputData = session()->get('__debugOutput');

        if ($outputData) {
            foreach ($outputData as $data) {
                $this->debugbar['messages']->debug($data);
            }
            session()->delete('__debugOutput');
        }
    }

    /**
     * Tab Queries
     * 
     * Outputs  the queries in Queries tab
     *
     * @return void
     */
    private function tabQueries()
    {
        $this->debugbar->addCollector(new MessagesCollector('queries'));

        $this->queries = ORM::get_query_log();
        if ($this->queries) {
            foreach ($this->queries as $query) {
                $this->debugbar['queries']->info($query);
            }
        }
    }

    /**
     * Tab Routes
     * 
     * Collects the routes
     *
     * @return void
     */
    private function tabRoutes($view)
    {
        $this->debugbar->addCollector(new MessagesCollector('routes'));
        
        $route = [
            'Route' => current_route(),
            'Pattern' => current_route_pattern(),
            'Uri' => current_route_uri(),
            'Method' => current_route_method(),
            'Module' => current_module(),
            'Middlewares' => current_middlewares(),
            'Controller' => current_module() . DS . current_controller(),
            'Action' => current_action(),
            'Args' => current_route_args(),
        ];

        if ($view) {
            $route['View'] = current_module() . DS . 'Views' . DS . $view;
        }

        $this->debugbar['routes']->info($route);
    }

    /**
     * Tab Mail
     * 
     * Outputs the mail log in Mail tab
     *
     * @return void
     */
    private function tabMailLog()
    {
        $this->debugbar->addCollector(new MessagesCollector('mail'));

        $mailLog = session()->get('__mailLog');
        if ($mailLog) {
            $logs = explode('&', $mailLog);
            foreach ($logs as $log) {
                $this->debugbar['mail']->info($log);
            }
            session()->delete('__mailLog');
        }
    }

}
