<?php

namespace Quantum\Tests\Unit\Libraries\Database\Adapters\Idiorm;

use Quantum\Tests\Unit\Libraries\Database\Adapters\DatabaseSeeder;
use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;
use Quantum\Libraries\Database\Database;
use Quantum\Tests\Unit\AppTestCase;

abstract class IdiormDbalTestCase extends AppTestCase
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

        config()->set('app.debug', true);

        IdiormDbal::connect(['driver' => 'sqlite', 'database' => ':memory:']);

        $this->createTables();

        $seeder = new DatabaseSeeder(IdiormDbal::class);

        $seeder->seed();
    }

    public function tearDown(): void
    {
        $this->deleteTables();

        IdiormDbal::disconnect();
    }

    private function createTables()
    {
        $this->createUserTable();
        $this->createUserProfessionTable();
        $this->createEventsTable();
        $this->createUserEventTable();
        $this->createUserMeetingsTable();
        $this->createTicketsTable();
        $this->createNotesTable();
    }

    private function deleteTables()
    {
        foreach ($this->tables as $table) {
            IdiormDbal::execute("DROP TABLE IF EXISTS $table");
        }
    }

    private function createUserTable()
    {
        IdiormDbal::execute("CREATE TABLE IF NOT EXISTS users (
                        id INTEGER PRIMARY KEY,
                        firstname VARCHAR(255),
                        lastname VARCHAR(255),
                        age INTEGER(11),
                        country VARCHAR(255),
                        created_at DATETIME
                    )");
    }

    private function createEventsTable()
    {
        IdiormDbal::execute("CREATE TABLE IF NOT EXISTS events (
                        id INTEGER PRIMARY KEY,
                        title VARCHAR(255),
                        country VARCHAR(255),
                        started_at DATETIME
                    )");
    }

    private function createUserEventTable()
    {
        IdiormDbal::execute("CREATE TABLE IF NOT EXISTS user_events (
                        id INTEGER PRIMARY KEY,
                        user_id INTEGER(11),
                        event_id INTEGER(11),
                        confirmed VARCHAR(3), 
                        created_at DATETIME
                    )");
    }

    private function createUserProfessionTable()
    {
        IdiormDbal::execute("CREATE TABLE IF NOT EXISTS user_professions (
                        id INTEGER PRIMARY KEY,
                        user_id INTEGER(11),
                        title VARCHAR(255)
                    )");
    }

    private function createUserMeetingsTable()
    {
        IdiormDbal::execute("CREATE TABLE IF NOT EXISTS user_meetings (
                        id INTEGER PRIMARY KEY,
                        user_id INTEGER (11),
                        title VARCHAR (255),
                        start_date DATETIME
        )");
    }

    private function createTicketsTable()
    {
        IdiormDbal::execute("CREATE TABLE IF NOT EXISTS tickets (
                        id INTEGER PRIMARY KEY,
                        meeting_id INTEGER (11),
                        type VARCHAR (255),
                        number VARCHAR (255) 
        )");
    }

    private function createNotesTable()
    {
        IdiormDbal::execute("CREATE TABLE IF NOT EXISTS notes (
                        id INTEGER PRIMARY KEY,
                        ticket_id INTEGER (11),
                        note text
        )");
    }
}