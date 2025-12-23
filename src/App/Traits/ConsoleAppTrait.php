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
 * @since 2.9.9
 */

namespace Quantum\App\Traits;

use Symfony\Component\Console\Application;
use Quantum\Console\CommandDiscovery;
use ReflectionException;
use Exception;

/**
 * Class ConsoleAppTrait
 * @package Quantum\App
 */
trait ConsoleAppTrait
{

    /**
     * @param string $name
     * @param string $version
     * @return Application
     */
    public function createApplication(string $name, string $version): Application
    {
        return new Application($name, $version);
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    private function registerCoreCommands()
    {
        $directory = framework_dir() . DS . 'Console' . DS . 'Commands';
        $namespace = '\\Quantum\\Console\\Commands\\';

        $this->registerCommands($directory, $namespace);
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    private function registerAppCommands()
    {
        $directory = base_dir() . DS . 'shared' . DS . 'Commands';
        $namespace = '\\Shared\\Commands\\';

        $this->registerCommands($directory, $namespace);
    }

    /**
     * @param string $directory
     * @param string $namespace
     * @throws ReflectionException
     */
    private function registerCommands(string $directory, string $namespace)
    {
        $commands = CommandDiscovery::discover($directory, $namespace);

        foreach ($commands as $command) {
            $this->application->add(new $command['class']());
        }
    }

    /**
     * @throws Exception
     */
    private function validateCommand(): void
    {
        $commandName = $this->input->getFirstArgument();

        if (!$this->application->has($commandName)) {
            throw new Exception("Command `$commandName` is not defined");
        }
    }
}