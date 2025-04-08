<?php

namespace Quantum\Tests\Unit\Libraries\Database\Factories;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Database\Factories\TableFactory;
use Quantum\Libraries\Database\Schemas\Table;
use Quantum\Libraries\Database\Database;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Loader\Setup;
use Mockery;

class TableFactoryTest extends AppTestCase
{

    private $tableFactory;
    private $table;

    public function setUp(): void
    {
        parent::setUp();

        if (!config()->has('database')) {
            config()->import(new Setup('config', 'database', true));
        }

        config()->set('database.default', 'sqlite');

        config()->set('debug', true);

        $this->tableFactory = new TableFactory();

        Mockery::getConfiguration()->setConstantsMap([
            'Quantum\Libraries\Database\Schemas\Table' => [
                'CREATE' => 1,
                'ALTER' => 2,
                'DROP' => 3,
                'RENAME' => 4,
            ]
        ]);

        $this->table = Mockery::mock('overload:Quantum\Libraries\Database\Schemas\Table');

        $this->table->shouldReceive('setAction')->andReturnUsing(function ($action) {
            return $this->table;
        });

        $this->table->shouldReceive('getAction')->andReturnUsing(function () {
            return $this->table->action;
        });

        $this->table->shouldReceive('save')->andReturn(0);
    }

    public function tearDown(): void
    {
        Database::execute("DROP TABLE IF EXISTS users");
    }

    public function testCreateTable()
    {
        $table = $this->tableFactory->create('users');

        $this->assertInstanceOf(Table::class, $table);
    }

    public function testAttemptToCreateTableWhichAlreadyExists()
    {
        Database::execute("CREATE TABLE users (
                        id INTEGER PRIMARY KEY,
                        firstname VARCHAR(255),
                        lastname VARCHAR(255),
                        age INTEGER(11),
                        country VARCHAR(255),
                        created_at DATETIME
                    )");

        $this->expectException(DatabaseException::class);

        $this->expectExceptionMessage('table_already_exists');

        $table = $this->tableFactory->create('users');
    }

    public function testGetTable()
    {
        Database::execute("CREATE TABLE IF NOT EXISTS users (
                        id INTEGER PRIMARY KEY,
                        firstname VARCHAR(255),
                        lastname VARCHAR(255),
                        age INTEGER(11),
                        country VARCHAR(255),
                        created_at DATETIME
                    )");

        $table = $this->tableFactory->get('users');

        $this->assertInstanceOf(Table::class, $table);
    }

    public function testAttemptToGetNonExistingTable()
    {
        $this->expectException(DatabaseException::class);

        $this->expectExceptionMessage('table_does_not_exists');

        $table = $this->tableFactory->get('users');
    }

    public function testRenameTable()
    {
        Database::execute("CREATE TABLE IF NOT EXISTS users (
                        id INTEGER PRIMARY KEY,
                        firstname VARCHAR(255),
                        lastname VARCHAR(255),
                        age INTEGER(11),
                        country VARCHAR(255),
                        created_at DATETIME
                    )");

        $this->assertTrue($this->tableFactory->rename('users', 'system_users'));
    }

    public function testAttemptToRenameNonExistingTable()
    {
        $this->expectException(DatabaseException::class);

        $this->expectExceptionMessage('table_does_not_exists');

        $this->tableFactory->rename('users', 'system_users');
    }

    public function testDropTable()
    {
        Database::execute("CREATE TABLE IF NOT EXISTS users (
                        id INTEGER PRIMARY KEY,
                        firstname VARCHAR(255),
                        lastname VARCHAR(255),
                        age INTEGER(11),
                        country VARCHAR(255),
                        created_at DATETIME
                    )");

        $this->assertTrue($this->tableFactory->drop('users'));
    }

    public function testAttemptToDropNonExistingTable()
    {
        $this->expectException(DatabaseException::class);

        $this->expectExceptionMessage('table_does_not_exists');

        $this->tableFactory->drop('users', 'system_users');
    }
}
