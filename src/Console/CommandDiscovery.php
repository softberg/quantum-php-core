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
 * @since 3.0.0
 */

namespace Quantum\Console;

use ReflectionException;
use ReflectionClass;

/**
 * Class CommandDiscovery
 * @package Quantum\Console
 */
class CommandDiscovery
{
    /**
     * @param string $directory
     * @param string $namespace
     * @return array
     * @throws ReflectionException
     */
    public static function discover(string $directory, string $namespace): array
    {
        $commands = [];

        foreach (get_directory_classes($directory) as $className) {
            $commandClass = $namespace . $className;

            if (!class_exists($commandClass)) {
                continue;
            }

            $commandReflection = new ReflectionClass($commandClass);

            if (!$commandReflection->isInstantiable() || !$commandReflection->isSubclassOf(QtCommand::class)) {
                continue;
            }

            $instance = $commandReflection->newInstance();

            $commands[] = [
                'class' => $commandClass,
                'name' => $instance->getName(),
                'description' => $instance->getDescription(),
                'help' => $instance->getHelp(),
            ];
        }

        return $commands;
    }
}
