<?php

declare(strict_types=1);

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

use Quantum\Migration\Exceptions\MigrationException;
use Quantum\Database\Exceptions\DatabaseException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Migration\MigrationManager;
use Quantum\Console\QtCommand;

/**
 * Class MigrationMigrateCommand
 * @package Quantum\Console
 */
class MigrationMigrateCommand extends QtCommand
{
    /**
     * The console command name.
     */
    protected ?string $name = 'migration:migrate';

    /**
     * The console command description.
     */
    protected ?string $description = 'Migrates the migrations';

    /**
     * Command arguments
     * @var array<int, array<int|string, mixed>>
     */
    protected array $args = [
        ['direction', 'optional', '[up] for upgrade, [down] for downgrade'],
    ];

    /**
     * Command options
     * @var array<int, array<int|string, mixed>>
     */
    protected array $options = [
        ['step', 's', 'optional', 'Number of migrations to apply'],
    ];

    /**
     * Executes the command
     * @throws DatabaseException|BaseException
     */
    public function exec(): void
    {

        $direction = $this->getArgument('direction') ?: MigrationManager::UPGRADE;

        if ($direction == 'down' && !$this->confirm('This operation will revert all the database changes, including the data. Continue?')) {
            $this->info('Operation was canceled!');
            return;
        }

        $step = (int) $this->getOption('step') ?: null;

        $migrationManager = new MigrationManager();

        try {
            $migrated = $migrationManager->applyMigrations($direction, $step);
            $this->info($migrated . ' migration' . ($migrated > 1 ? 's were' : ' was') . ' applied');
        } catch (MigrationException $e) {
            $this->info($e->getMessage());
        }
    }
}
