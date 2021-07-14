<?php


namespace Quantum;

use Quantum\Debugger\Debugger;
use Quantum\Di\Di;
use Quantum\Tracer\ErrorHandler;
use Quantum\Bootstrap;

class App
{

    public static function start()
    {
        self::loadCoreFuncations();

        Di::loadDefinitions();

        ErrorHandler::setup();

        Debugger::initStore();

        Bootstrap::run();
    }

    public static function loadCoreFuncations()
    {
        foreach (glob(HELPERS_DIR . DS . 'functions' . DS . '*.php') as $filename) {
            require_once $filename;
        }
    }

}