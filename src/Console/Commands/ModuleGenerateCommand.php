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

namespace Quantum\Console\Commands;

use Symfony\Component\VarExporter\Exception\ExceptionInterface;
use Quantum\Module\ModuleManager;
use Quantum\Console\QtCommand;
use Exception;

/**
 * Class ModuleGenerateCommand
 * @package Quantum\Console\Commands
 */
class ModuleGenerateCommand extends QtCommand
{
    /**
     * Command name
     * @var string
     */
    protected $name = 'module:generate';

    /**
     * Command description
     * @var string
     */
    protected $description = 'Generate new module';

    /**
     * Command arguments
     * @var string[][]
     */
    protected $args = [
        ['module', 'required', 'The module name'],
    ];

    /**
     * Command help text
     * @var string
     */
    protected $help = 'The command will create files for new module';

    /**
     * Command options
     * @var array
     */
    protected $options = [
        ['yes', 'y', 'none', 'Module enabled status'],
        ['template', 't', 'optional', 'The module template', 'DefaultWeb'],
        ['with-assets', 'a', 'none', 'Install module will assets'],
    ];

    /**
     * Executes the command
     * @throws ExceptionInterface
     */
    public function exec()
    {
        try {
            $moduleName = $this->getArgument('module');

            $moduleManager = new ModuleManager(
                $moduleName,
                $this->getOption('template'),
                $this->getOption('yes'),
                $this->getOption('with-assets')
            );

            $moduleManager->writeContents();

            $moduleManager->addModuleConfig();

            $this->info($moduleName . ' module successfully created');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
