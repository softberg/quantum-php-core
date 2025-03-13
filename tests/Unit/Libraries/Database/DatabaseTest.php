<?php

namespace Quantum\Tests\Unit\Libraries\Database;

use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;
use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Libraries\Database\Database;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Loader\Setup;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class DatabaseTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        config()->import(new Setup('config', 'database', true));

        config()->set('database.default', 'sqlite');

        config()->set('debug', true);

        Database::execute("CREATE TABLE users (
                        id INTEGER PRIMARY KEY,
                        firstname VARCHAR(255),
                        lastname VARCHAR(255),
                        age INTEGER(11),
                        country VARCHAR(255),
                        created_at DATETIME
                    )");
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

    public function testDatabaseGetOrm()
    {
        $db = Database::getInstance();

        $this->assertInstanceOf(DbalInterface::class, $db->getOrm('user'));
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

}
