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
            \Quantum\Libraries\Database\Schemas\Table::class => [
                'CREATE' => 1,
                'ALTER' => 2,
                'DROP' => 3,
                'RENAME' => 4,
            ],
        ]);

        $this->table = Mockery::mock('overload:Quantum\Libraries\Database\Schemas\Table');

        $this->table->shouldReceive('setAction')->andReturnUsing(fn ($action) => $this->table);

        $this->table->shouldReceive('getAction')->andReturnUsing(fn () => $this->table->action);

        $this->table->shouldReceive('save')->andReturn(0);
    }

    public function tearDown(): void
    {
        Database::execute('DROP TABLE IF EXISTS profiles');
    }

    public function testCreateTable()
    {
        $table = $this->tableFactory->create('profiles');

        $this->assertInstanceOf(Table::class, $table);
    }

    public function testAttemptToCreateTableWhichAlreadyExists()
    {
        Database::execute('CREATE TABLE profiles (
                        id INTEGER PRIMARY KEY,
                        firstname VARCHAR(255),
                        lastname VARCHAR(255),
                        age INTEGER(11),
                        country VARCHAR(255),
                        created_at DATETIME
                    )');

        $this->expectException(DatabaseException::class);

        $this->expectExceptionMessage('The table `profiles` is already exists');

        $this->tableFactory->create('profiles');
    }

    public function testGetTable()
    {
        Database::execute('CREATE TABLE IF NOT EXISTS profiles (
                        id INTEGER PRIMARY KEY,
                        firstname VARCHAR(255),
                        lastname VARCHAR(255),
                        age INTEGER(11),
                        country VARCHAR(255),
                        created_at DATETIME
                    )');

        $table = $this->tableFactory->get('profiles');

        $this->assertInstanceOf(Table::class, $table);
    }

    public function testAttemptToGetNonExistingTable()
    {
        $this->expectException(DatabaseException::class);

        $this->expectExceptionMessage('The table `profiles` does not exists');

        $table = $this->tableFactory->get('profiles');
    }

    public function testRenameTable()
    {
        Database::execute('CREATE TABLE IF NOT EXISTS profiles (
                        id INTEGER PRIMARY KEY,
                        firstname VARCHAR(255),
                        lastname VARCHAR(255),
                        age INTEGER(11),
                        country VARCHAR(255),
                        created_at DATETIME
                    )');

        $this->assertTrue($this->tableFactory->rename('profiles', 'system_users'));
    }

    public function testAttemptToRenameNonExistingTable()
    {
        $this->expectException(DatabaseException::class);

        $this->expectExceptionMessage('The table `profiles` does not exists');

        $this->tableFactory->rename('profiles', 'system_users');
    }

    public function testDropTable()
    {
        Database::execute('CREATE TABLE IF NOT EXISTS profiles (
                        id INTEGER PRIMARY KEY,
                        firstname VARCHAR(255),
                        lastname VARCHAR(255),
                        age INTEGER(11),
                        country VARCHAR(255),
                        created_at DATETIME
                    )');

        $this->assertTrue($this->tableFactory->drop('profiles'));
    }

    public function testAttemptToDropNonExistingTable()
    {
        $this->expectException(DatabaseException::class);

        $this->expectExceptionMessage('The table `profiles` does not exists');

        $this->tableFactory->drop('profiles');
    }
}
