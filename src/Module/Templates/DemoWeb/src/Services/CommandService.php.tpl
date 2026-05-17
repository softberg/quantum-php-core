<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman@quantumphp.io>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link https://quantumphp.io/
 * @since 2.9.9
 */


namespace {{MODULE_NAMESPACE}}\Services;

use Quantum\Console\CommandDiscovery;
use Quantum\Service\Service;

/**
 * Class CommandService
 * @package Modules\{{MODULE_NAME}}
 */
class CommandService extends Service
{

    /**
     * Get all available commands (core + app)
     * @throws ReflectionException
     */
    public function getAllCommands(): array
    {
        $coreCommands = CommandDiscovery::discover(
            framework_dir() . DS . 'Console' . DS . 'Commands',
            '\\Quantum\\Console\\Commands\\'
        );

        $appCommands = CommandDiscovery::discover(
            base_dir() . DS . 'shared' . DS . 'Commands',
            '\\Shared\\Commands\\'
        );

        return array_merge($coreCommands, $appCommands);
    }
}
