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

namespace Quantum\Console;

use Symfony\Component\Console\Application;
use Quantum\Loader\Loader;
use Quantum\Di\Di;
use Quantum\App;

/**
 * Class QtConsole
 * @package Quantum\Console
 */
class QtConsole
{

    /**
     * Console application name
     * @var string
     */
    private $name = 'Qt Console Application';

    /**
     * Console application version
     * @var string
     */
    private $version = '2.0.0';

    /**
     * Console application instance
     * @var Application
     */
    private $application;

    /**
     * Initialize the console application
     * @return mixed
     */
    public function init()
    {

        App::loadCoreFuncations();

        Di::loadDefinitions();

        $loader = Di::get(Loader::class);

        $loader->loadDir(base_dir() . DS . 'helpers');
        $loader->loadDir(base_dir() . DS . 'libraries');

        $this->application = new Application($this->name, $this->version);

        $this->register();

        return $this->application->run();
    }

    /**
     * Registers commands
     * @return void
     */
    private function register()
    {
        $coreClassNames = get_directory_classes(CORE_DIR . DS . 'Console' . DS . 'Commands');

        foreach ($coreClassNames as $coreClassName) {
            $coreCommandClass = '\\Quantum\\Console\\Commands\\' . $coreClassName;

            $coreCommand = new $coreCommandClass();

            if ($coreCommand instanceof QtCommand) {
                $this->application->add($coreCommand);
            }
        }

        $classNames = get_directory_classes(BASE_DIR . DS . 'base' . DS . 'Commands');
        foreach ($classNames as $className) {
            $commandClass = '\\Base\\Commands\\' . $className;

            $command = new $commandClass();

            if ($command instanceof QtCommand) {
                $this->application->add($command);
            }
        }
    }

}
