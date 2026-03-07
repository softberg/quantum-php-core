<?php

namespace Quantum\Tests\Unit\Database\Adapters\Sleekdb;

use Quantum\Tests\Unit\Database\Adapters\DatabaseSeeder;
use Quantum\Database\Adapters\Sleekdb\SleekDbal;
use Quantum\Database\Database;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Loader\Setup;

abstract class SleekDbalTestCase extends AppTestCase
{
    private $tables = [
        'users',
        'profiles',
        'user_professions',
        'events',
        'user_events',
        'user_meetings',
        'tickets',
        'notes',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->setPrivateProperty(Database::class, 'instance', null);

        if (!config()->has('database')) {
            config()->import(new Setup('config', 'database'));
        }

        config()->set('database.default', 'sleekdb');

        SleekDbal::connect(config()->get('database.sleekdb'));

        $seeder = new DatabaseSeeder();

        $seeder->seed();
    }

    public function tearDown(): void
    {
        $this->deleteTables();

        SleekDbal::disconnect();
    }

    public function deleteTables(): void
    {
        foreach ($this->tables as $table) {
            $model = new SleekDbal($table);
            $model->truncate();
        }
    }
}
