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
 * @since 2.9.5
 */

namespace Quantum\Console\Commands;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Lang\Exceptions\LangException;
use Quantum\Migration\Exceptions\MigrationException;
use Quantum\Migration\MigrationManager;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Console\QtCommand;
use ReflectionException;

/**
 * Class MigrationMigrateCommand
 * @package Quantum\Console
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
     * @throws BaseException
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
