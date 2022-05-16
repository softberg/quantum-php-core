<?php

namespace Quantum\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Quantum\Libraries\Database\Schema\Table;
use Quantum\Exceptions\MigrationException;
use Quantum\Libraries\Database\Database;
use Quantum\Factory\TableFactory;
use Quantum\Loader\Setup;
use Quantum\Di\Di;
use Quantum\App;
use Mockery;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class TableFactoryTest extends TestCase
{

    private $tableFactory;
    private $table;

    public function setUp(): void
    {
        App::loadCoreFunctions(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers');

        App::setBaseDir(dirname(__DIR__) . DS . '_root');

        Di::loadDefinitions();

        if (!config()->has('database')) {
            config()->import(new Setup('shared' . DS . 'config', 'database', true));
        }

        config()->set('database.current', 'sqlite');

        config()->set('debug', true);

        $this->tableFactory = new TableFactory();

        Mockery::getConfiguration()->setConstantsMap([
            'Quantum\Libraries\Database\Schema\Table' => [
                'CREATE' => 1,
                'ALTER' => 2,
                'DROP' => 3,
                'RENAME' => 4,
            ]
        ]);

        $this->table = Mockery::mock('overload:Quantum\Libraries\Database\Schema\Table');

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

        $this->expectException(MigrationException::class);

        $this->expectExceptionMessage('The table `users` is already exists');

        $table = $this->tableFactory->create('users');
    }

    public function testGetTable()
    {
        Database::execute("CREATE TABLE users (
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
        $this->expectException(MigrationException::class);

        $this->expectExceptionMessage('The table `users` does not exists');

        $table = $this->tableFactory->get('users');
    }

    public function testRenameTable()
    {
        Database::execute("CREATE TABLE users (
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
        $this->expectException(MigrationException::class);

        $this->expectExceptionMessage('The table `users` does not exists');

        $this->tableFactory->rename('users', 'system_users');
    }

    public function testDropTable()
    {
        Database::execute("CREATE TABLE users (
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
        $this->expectException(MigrationException::class);

        $this->expectExceptionMessage('The table `users` does not exists');

        $this->tableFactory->drop('users', 'system_users');
    }

}
