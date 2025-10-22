<?php

namespace Quantum\Tests\Unit\Libraries\Database;

use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;
use Quantum\Libraries\Database\Database;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Loader\Setup;

class DatabaseTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        if (!config()->has('database')) {
            config()->import(new Setup('config', 'database', true));
        }

        config()->set('database.default', 'sqlite');

        config()->set('debug', true);

        Database::execute("CREATE TABLE IF NOT EXISTS users (
                        id INTEGER PRIMARY KEY,
                        firstname VARCHAR(255),
                        lastname VARCHAR(255),
                        age INTEGER(11),
                        country VARCHAR(255),
                        created_at DATETIME
                    )");
    }

    public function tearDown(): void
    {
        Database::execute("DROP TABLE IF EXISTS users");
    }

    public function testDatabaseInstance()
    {
        $db1 = Database::getInstance();

        $db2 = Database::getInstance();

        $this->assertInstanceOf(Database::class, $db1);

        $this->assertSame($db1, $db2);
    }

    public function testDatabaseGetConfigs()
    {
        $this->assertEquals(config()->get('database.sqlite'), Database::getInstance()->getConfigs());
    }

    public function testDatabaseGetOrmClass()
    {
        $this->assertEquals(IdiormDbal::class, Database::getInstance()->getOrmClass());
    }

    public function testDatabaseRawQueries()
    {
        $result = Database::query('SELECT * FROM users WHERE id=:id', ['id' => 1]);

        $this->assertIsArray($result);

        $this->assertEmpty($result);

        Database::execute('INSERT INTO users (firstname, lastname, age, country)
                                     VALUES (:firstname, :lastname, :age, :country)',
                ['firstname' => 'John', 'lastname' => 'Doe', 'age' => '56', 'country' => 'Spain']);

        $result = Database::query('SELECT * FROM users WHERE id=:id', ['id' => 1]);

        $this->assertNotEmpty($result);

        $this->assertEquals('John', $result[0]['firstname']);

        $this->assertEquals('Doe', $result[0]['lastname']);

        $this->assertEquals("SELECT * FROM users WHERE '1'=:'1'", Database::lastQuery());

        $this->assertIsArray(Database::queryLog());
    }

    public function testDatabaseTransactionMethods()
    {
        Database::beginTransaction();

        Database::execute('INSERT INTO users (firstname, lastname, age, country)
                                 VALUES (:firstname, :lastname, :age, :country)', [
            'firstname' => 'Alice',
            'lastname' => 'Smith',
            'age' => 30,
            'country' => 'Canada'
        ]);

        Database::rollback();

        $result = Database::query('SELECT * FROM users WHERE firstname = :firstname', ['firstname' => 'Alice']);

        $this->assertEmpty($result);

        Database::beginTransaction();

        Database::execute('INSERT INTO users (firstname, lastname, age, country)
                                 VALUES (:firstname, :lastname, :age, :country)', [
            'firstname' => 'Bob',
            'lastname' => 'Marley',
            'age' => 42,
            'country' => 'Jamaica'
        ]);

        Database::commit();

        $result = Database::query('SELECT * FROM users WHERE firstname = :firstname', ['firstname' => 'Bob']);

        $this->assertNotEmpty($result);

        $this->assertEquals('Marley', $result[0]['lastname']);
    }

    public function testDatabaseTransactionClosureSuccess()
    {
        $result = Database::transaction(function () {
            Database::execute('INSERT INTO users (firstname, lastname, age, country)
                                     VALUES (:firstname, :lastname, :age, :country)', [
                'firstname' => 'Charlie',
                'lastname' => 'Brown',
                'age' => 28,
                'country' => 'USA'
            ]);

            return 'committed';
        });

        $this->assertEquals('committed', $result);

        $rows = Database::query('SELECT * FROM users WHERE firstname = :firstname', ['firstname' => 'Charlie']);

        $this->assertNotEmpty($rows);

        $this->assertEquals('Brown', $rows[0]['lastname']);
    }

    public function testDatabaseTransactionClosureFailure()
    {
        try {
            Database::transaction(function () {
                Database::execute('INSERT INTO users (firstname, lastname, age, country)
                                         VALUES (:firstname, :lastname, :age, :country)', [
                    'firstname' => 'Eve',
                    'lastname' => 'Hacker',
                    'age' => 999,
                    'country' => 'Matrix'
                ]);

                throw new \Exception('Something went wrong!');
            });
        } catch (\Exception $e) {
            $this->assertEquals('Something went wrong!', $e->getMessage());
        }

        $rows = Database::query('SELECT * FROM users WHERE firstname = :firstname', ['firstname' => 'Eve']);

        $this->assertEmpty($rows);
    }
}
