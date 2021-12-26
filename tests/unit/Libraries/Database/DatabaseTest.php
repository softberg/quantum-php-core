<?php

namespace Quantum\Tests\Libraries\Database;

use PHPUnit\Framework\TestCase;
use Quantum\Libraries\Database\DbalInterface;
use Quantum\Libraries\Database\Database;
use Quantum\Loader\Setup;
use Quantum\Di\Di;
use Quantum\App;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class DatabaseTest extends TestCase
{

    public function setUp(): void
    {
        App::loadCoreFunctions(dirname(__DIR__, 4) . DS . 'src' . DS . 'Helpers');

        App::setBaseDir(dirname(__DIR__, 2) . DS . '_root');

        Di::loadDefinitions();

        config()->import(new Setup('config', 'database', true));

        config()->set('database.current', 'sqlite');

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

    public function testGetOrm()
    {
        $db = Database::getInstance();

        $this->assertInstanceOf(DbalInterface::class, $db->getOrm('user'));
    }

    public function testRawQueries()
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
