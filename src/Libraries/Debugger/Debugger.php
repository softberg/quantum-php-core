<?php
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

class Debugger extends DebugBar
{
    public static $debugbar;
    public static $debugbarRenderer = false;
    public static $queries;
    public static $dump_message;
    public static $assets_url;

    public function __construct()
    {
        $this->addCollector(new PhpInfoCollector());
        $this->addCollector(new MessagesCollector());
        $this->addCollector(new RequestDataCollector());
        $this->addCollector(new MemoryCollector());
    }

    public static function runDebuger($view=null)
    {
        self::$debugbar = new Debugger();
        self::$assets_url = base_url() . '/assets/DebugBar/Resources';
        self::addQueries();
        self::addOut();
        self::addRoute($view);

        self::$debugbarRenderer = self::$debugbar->getJavascriptRenderer()->setBaseUrl( self::$assets_url);
        return  self::$debugbarRenderer;
    }

    private static function addQueries()
    {
        self::$debugbar->addCollector(new MessagesCollector('queries'));
        self::$queries = ORM::get_query_log();
        if(self::$queries) {
            foreach (self::$queries as $query) {
                self::$debugbar['queries']->info($query);
            }
        }

    }

    private static function addRoute($view)
    {
        $uri = RouteController::$currentRoute['uri'];
        $method = RouteController::$currentRoute['method'];
        $module = RouteController::$currentRoute['module'];
        $current_controller = 'modules'. DS . $module . DS . RouteController::$currentRoute['controller'];
        $current_action = RouteController::$currentRoute['action'];

        self::$debugbar->addCollector(new MessagesCollector('routes'));
        self::$debugbar['routes']->info('Route => ' .  $uri);
        self::$debugbar['routes']->info('Method => ' .  $method);
        self::$debugbar['routes']->info('Module => ' .  $module);
        self::$debugbar['routes']->info('Controller => ' .  $current_controller);
        self::$debugbar['routes']->info('Action => ' .  $current_action);
        if($view) {
            self::$debugbar['routes']->info('View => ' . 'modules/'. RouteController::$currentRoute['module'] . '/Views/' . $view);
        }
    }

    private static function addOut()
    {
        if(session()->get('output')){
            self::$debugbar['messages']->debug(session()->get('output'));
            session()->delete('output');
        }
    }
}