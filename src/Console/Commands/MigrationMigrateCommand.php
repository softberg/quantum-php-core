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
 * @since 2.9.0
 */

namespace Quantum\Console\Commands;

use Quantum\Exceptions\MigrationException;
use Quantum\Exceptions\DatabaseException;
use Quantum\Exceptions\ConfigException;
use Quantum\Migration\MigrationManager;
use Quantum\Exceptions\LangException;
use Quantum\Exceptions\AppException;
use Quantum\Exceptions\DiException;
use Quantum\Console\QtCommand;
use ReflectionException;

/**
 * Class MigrationMigrateCommand
 * @package Quantum\Console\Command
 */
class MigrationMigrateCommand extends QtCommand
{

    /**
     * The console command name.
     * @var string
     */
    protected $name = 'migration:migrate';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Migrates the migrations';

    /**
     * Command arguments
     * @var string[][]
     */
    protected $args = [
        ['direction', 'optional', '[up] for upgrade, [down] for downgrade'],
    ];

    /**
     * Command options
     * @var string[][]
     */
    protected $options = [
        ['step', 's', 'optional', 'Number of migrations to apply'],
    ];

    /**
     * Executes the command
     * @throws AppException
     * @throws ConfigException
     * @throws DatabaseException
     * @throws DiException
     * @throws LangException
     * @throws ReflectionException
     */
    public function exec()
    {

        $direction = $this->getArgument('direction') ?: MigrationManager::UPGRADE;

        if ($direction == 'down') {
            if (!$this->confirm("This operation will revert all the database changes, including the data. Continue?")) {
                $this->info('Operation was canceled!');
                return;
            }
        }

        $step = (int)$this->getOption('step') ?: null;

        $migrationManager = new MigrationManager();

        try {
            $migrated = $migrationManager->applyMigrations($direction, $step);
            $this->info($migrated . ' migration' . ($migrated > 1 ? 's were' : ' was') . ' applied');
        } catch (MigrationException $e) {
            $this->info($e->getMessage());
        }
    }
}
