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
 * @since 1.7.0
 */

namespace Quantum\Console;

use Symfony\Component\Console\Application;

/**
 * Class Qt_Console
 * @package Quantum\Console
 */
class Qt_Console
{
    /**
     * Console application name
     *
     * @var string
     */
    private $name = 'Qt Console Application';

    /**
     * Console application version
     *
     * @var string
     */
    private $version = '1.0.0';

    /**
     * Console application instance
     *
     * @var
     */
    private $application;

    /**
     * Initialise the console applicaiotn
     *
     * @return mixed
     */
    public function init()
    {
        $this->application = new Application($this->name, $this->version);

        $this->register();

        return $this->run();
    }

    /**
     * Registers commands
     *
     * @return void
     */
    private function register()
    {
        $coreClassNames = get_directory_classes(CORE_DIR . DS . 'Console' . DS . 'Commands');
        foreach ($coreClassNames as $coreClassName) {
            $coreCommandClass = '\\Quantum\\Console\\Commands\\' . $coreClassName;

            $coreCommand = new $coreCommandClass();

            if ($coreCommand instanceof Qt_Command) {
                $this->application->add($coreCommand);
            }
        }

        $classNames = get_directory_classes(BASE_DIR . DS . 'base' . DS . 'commands');
        foreach ($classNames as $className) {
            $commandClass = '\\Base\\Commands\\' . $className;

            $command = new $commandClass();

            if ($command instanceof Qt_Command) {
                $this->application->add($command);
            }
        }
    }

    /**
     * Runs the application
     *
     * @return mixed
     */
    private function run()
    {
        return $this->application->run();
    }
}