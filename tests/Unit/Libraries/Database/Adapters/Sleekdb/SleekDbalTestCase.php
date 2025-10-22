<?php

namespace Quantum\Tests\Unit\Libraries\Database\Adapters\Sleekdb;

use Quantum\Tests\Unit\Libraries\Database\Adapters\DatabaseSeeder;
use Quantum\Libraries\Database\Adapters\Sleekdb\SleekDbal;
use Quantum\Libraries\Database\Database;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Loader\Setup;

abstract class SleekDbalTestCase extends AppTestCase
{

    private $tables = [
        'users',
        'user_professions',
        'events',
        'user_events',
        'user_meetings',
        'tickets',
        'notes'
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

        $seeder = new DatabaseSeeder(SleekDbal::class);

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
            $model->deleteTable();
        }
    }
}