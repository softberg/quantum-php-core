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
 * @since 2.8.0
 */

namespace Quantum\Console\Commands;

use Quantum\Exceptions\MigrationException;
use Quantum\Migration\MigrationManager;
use Quantum\Console\QtCommand;

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
     * @throws \Quantum\Exceptions\AppException
     * @throws \Quantum\Exceptions\ConfigException
     * @throws \Quantum\Exceptions\DatabaseException
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\LangException
     * @throws \ReflectionException
     */
    public function exec()
    {
        if ($this->getArgument('direction') == 'down') {
            if (!$this->confirm("This operation will revert all the database changes, including the data. Continue?")) {
                $this->info('Operation was canceled!');
                return;
            }
        }

        $direction = $this->getArgument('direction') ?: MigrationManager::UPGRADE;
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
