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

use Quantum\Migration\MigrationManager;
use Quantum\Console\QtCommand;

/**
 * Class MigrationCreateCommand
 * @package Quantum\Console\Commands
 */
class MigrationGenerateCommand extends QtCommand
{

    /**
     * The console command name.
     * @var string
     */
    protected $name = 'migration:generate';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Generates new migration file';

    /**
     * Command arguments
     * @var string[][]
     */
    protected $args = [
        ['action', 'required', 'The action to perform. [create] for creating table, [alter] for altering table, [rename] for renaming table, [drop] for dropping table'],
        ['table', 'required', 'The table name'],
    ];

    /**
     * Executes the command
     * @throws \Quantum\Exceptions\MigrationException
     */
    public function exec()
    {
        $migrationManager = new MigrationManager();

        $migrationName = $migrationManager->generateMigration($this->getArgument('table'), $this->getArgument('action'));

        $this->info('Migration file ' . $migrationName . ' successfully created');
    }

}
