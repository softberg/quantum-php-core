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

namespace Quantum\Debugger;

use DebugBar\DebugBar;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use Quantum\Libraries\Database\Database as DB;

/**
 * Debugger class
 * @package Quantum\Debugger
 * @uses DebugBar
 */
class Debugger extends DebugBar
{

    /**
     * Debugbar instance
     * @var object
     */
    private $debugbar;

    /**
     * Assets url
     * @var string
     */
    private $assetsUrl = '/assets/DebugBar/Resources';

    /**
     * Custom CSS
     * 
     */
    private $customCss = 'custom_debugbar.css';

    /**
     * Class constructor
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

        return $this->debugbar->getJavascriptRenderer()
                        ->setBaseUrl(base_url() . $this->assetsUrl)
                        ->addAssets([$this->customCss], []);
    }

    /**
     * Tab Messages
     * Output debug messages in Messages tab
     */
    private function tabMessages()
    {
        $outputData = session()->get('_qt_debug_output');

        if ($outputData) {
            foreach ($outputData as $data) {
                $this->debugbar['messages']->debug($data);
            }
            session()->delete('_qt_debug_output');
        }
    }

    /**
     * Tab Queries
     * Outputs the query log in Queries tab
     */
    private function tabQueries()
    {
        $this->debugbar->addCollector(new MessagesCollector('queries'));

        $queryLog = DB::getQueryLog();

        if ($queryLog) {
            foreach ($queryLog as $query) {
                $this->debugbar['queries']->info($query);
            }
        }
    }

    /**
     * Tab Routes
     * Collects the routes
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
     * Outputs the mail log in Mail tab
     */
    private function tabMailLog()
    {
        $this->debugbar->addCollector(new MessagesCollector('mail'));

        $mailLog = session()->get('_qt_mailer_log');
        if ($mailLog) {
            $logs = explode('&', $mailLog);
            foreach ($logs as $log) {
                $this->debugbar['mail']->info($log);
            }
            session()->delete('_qt_mailer_log');
        }
    }

}
