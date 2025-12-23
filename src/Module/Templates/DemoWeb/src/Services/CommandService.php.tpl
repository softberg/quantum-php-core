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


namespace {{MODULE_NAMESPACE}}\Services;

use Quantum\Console\CommandDiscovery;
use Quantum\Service\QtService;

/**
 * Class CommandService
 * @package Modules\{{MODULE_NAME}}
 */
class CommandService extends QtService
{

    /**
     * Get all available commands (core + app)
     * @return array
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