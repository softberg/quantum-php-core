<?php

namespace Quantum\Tests\Unit\Database\Adapters\Idiorm;

use Quantum\Tests\Unit\Database\Adapters\DatabaseSeeder;
use Quantum\Database\Adapters\Idiorm\IdiormDbal;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Database\Database;

abstract class IdiormDbalTestCase extends AppTestCase
{
    private array $tables = [
        'users',
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

        config()->set('app.debug', true);

        IdiormDbal::connect(['driver' => 'sqlite', 'database' => ':memory:']);

        $this->createTables();

        $seeder = new DatabaseSeeder();

        $seeder->seed();
    }

    public function tearDown(): void
    {
        $this->deleteTables();

        IdiormDbal::disconnect();
    }

    private function createTables(): void
    {
        $this->createUserTable();
        $this->createProfileTable();
        $this->createUserTable();
        $this->createUserProfessionTable();
        $this->createEventsTable();
        $this->createUserEventTable();
        $this->createUserMeetingsTable();
        $this->createTicketsTable();
        $this->createNotesTable();
    }

    private function deleteTables(): void
    {
        foreach ($this->tables as $table) {
            IdiormDbal::execute("DROP TABLE IF EXISTS $table");
        }
    }

    private function createUserTable(): void
    {
        IdiormDbal::execute('CREATE TABLE IF NOT EXISTS users (
                        id INTEGER PRIMARY KEY,
                        email VARCHAR(255),
                        password VARCHAR(255)
                    )');
    }

    private function createProfileTable(): void
    {
        IdiormDbal::execute('CREATE TABLE IF NOT EXISTS profiles (
                        id INTEGER PRIMARY KEY,
                        user_id INTEGER(11),
                        firstname VARCHAR(255),
                        lastname VARCHAR(255),
                        age INTEGER(11),
                        country VARCHAR(255),
                        created_at DATETIME
                    )');
    }

    private function createEventsTable(): void
    {
        IdiormDbal::execute('CREATE TABLE IF NOT EXISTS events (
                        id INTEGER PRIMARY KEY,
                        title VARCHAR(255),
                        country VARCHAR(255),
                        started_at DATETIME
                    )');
    }

    private function createUserEventTable(): void
    {
        IdiormDbal::execute('CREATE TABLE IF NOT EXISTS user_events (
                        id INTEGER PRIMARY KEY,
                        user_id INTEGER(11),
                        event_id INTEGER(11),
                        confirmed VARCHAR(3), 
                        created_at DATETIME
                    )');
    }

    private function createUserProfessionTable(): void
    {
        IdiormDbal::execute('CREATE TABLE IF NOT EXISTS user_professions (
                        id INTEGER PRIMARY KEY,
                        user_id INTEGER(11),
                        title VARCHAR(255)
                    )');
    }

    private function createUserMeetingsTable(): void
    {
        IdiormDbal::execute('CREATE TABLE IF NOT EXISTS user_meetings (
                        id INTEGER PRIMARY KEY,
                        user_id INTEGER (11),
                        title VARCHAR (255),
                        start_date DATETIME
        )');
    }

    private function createTicketsTable(): void
    {
        IdiormDbal::execute('CREATE TABLE IF NOT EXISTS tickets (
                        id INTEGER PRIMARY KEY,
                        meeting_id INTEGER (11),
                        type VARCHAR (255),
                        number VARCHAR (255) 
        )');
    }

    private function createNotesTable(): void
    {
        IdiormDbal::execute('CREATE TABLE IF NOT EXISTS notes (
                        id INTEGER PRIMARY KEY,
                        ticket_id INTEGER (11),
                        note text
        )');
    }
}
