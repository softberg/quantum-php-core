<?php

namespace Quantum\Models {

    use Quantum\Mvc\QtModel;

    class UserModel extends QtModel
    {

        public $table = 'user';
        protected $fillable = [
            'firstname',
            'lastname'
        ];

    }

}

namespace Quantum\Libraries\Database {

    if (!function_exists('modules_dir')) {

        function modules_dir()
        {
            return __DIR__ . DS . 'modules';
        }

    }

    if (!function_exists('current_module')) {

        function current_module()
        {
            return 'test';
        }

    }

    if (!function_exists('base_dir')) {

        function base_dir()
        {
            return '/';
        }

    }
}

namespace Quantum\Test\Unit {

    use Mockery;
    use PHPUnit\Framework\TestCase;
    use Quantum\Exceptions\ModelException;
    use Quantum\Models\UserModel;
    use Quantum\Libraries\Database\Database;
    use Quantum\Exceptions\DatabaseException;
    use Quantum\Libraries\Storage\FileSystem;
    use Quantum\Loader\Loader;

    /**
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    class DatabaseTest extends TestCase
    {

        private $dbConfigs = [
            'current' => 'mysql',
            'mysql' => array(
                'driver' => 'mysql',
                'host' => 'localhost',
                'dbname' => 'database',
                'username' => 'username',
                'password' => 'password',
                'charset' => 'charset',
            ),
            'sqlite' => array(
                'driver' => 'sqlite',
                'database' => 'database.sqlite',
                'prefix' => '',
            ),
        ];
        private $queries = [
            'UPDATE users WHERE id=:id',
            'SELECT * FROM users WHERE id=:id'
        ];
        private $resultUser = [
            'id' => 1,
            'firstname' => 'John',
            'lastname' => 'Doe'
        ];
        private $database;
        private $idiormDbalMock;
        private $helperMock;

        public function setUp(): void
        {

            $loader = new Loader(new FileSystem);

            $loader->loadDir(dirname(__DIR__, 4) . DS . 'src' . DS . 'Helpers' . DS . 'functions');
            
            $loaderMock = Mockery::mock('Quantum\Loader\Loader');

            $this->idiormDbalMock = Mockery::mock('overload:Quantum\Libraries\Database\IdiormDbal');

            $loaderMock->shouldReceive('setup')->andReturn($loaderMock);

            $loaderMock->shouldReceive('load')->andReturn($this->dbConfigs);

            $this->database = new Database($loaderMock);

            $this->idiormDbalMock->shouldReceive('dbConnect')->andReturn(['connection_string']);

            $this->idiormDbalMock->shouldReceive('execute')->withSomeOfArgs($this->queries[0], ['id' => 1])->andReturn(true);

            $this->idiormDbalMock->shouldReceive('query')->withSomeOfArgs($this->queries[1], ['id' => 1])->andReturn($this->resultUser);

            $this->idiormDbalMock->shouldReceive('lastQuery')->andReturn('SELECT * FROM users WHERE id=1');

            $this->idiormDbalMock->shouldReceive('lastStatement')->andReturn($this->queries[1]);

            $this->idiormDbalMock->shouldReceive('queryLog')->andReturn($this->queries);
        }

        public function tearDown(): void
        {
            Mockery::close();
        }

        public function testGetORM()
        {
            $this->assertInstanceOf(\Quantum\Libraries\Database\IdiormDbal::class, $this->database->getORM(UserModel::class, 'test'));
        }

        public function testGetORMWithoutTableDefined()
        {
            $this->expectException(ModelException::class);

            $this->expectExceptionMessage('Model `' . UserModel::class . '` does not have $table property defined');

            $this->database->getORM('', UserModel::class);
        }

        public function testConnectAndConnected()
        {
            $this->assertFalse($this->database->connected());

            $this->database->connect($this->idiormDbalMock);

            $this->assertTrue($this->database->connected());
        }

        public function testGetDbalClass()
        {
            $this->assertEquals(\Quantum\Libraries\Database\IdiormDbal::class, $this->database->getDbalClass());
        }

        public function testCommonMethods()
        {
            $this->assertTrue($this->database::execute('UPDATE users WHERE id=:id', ['id' => 1]));

            $this->assertIsArray($this->database::query('SELECT * FROM users WHERE id=:id', ['id' => 1]));

            $this->assertIsString($this->database::lastQuery());

            $this->assertIsString($this->database::lastStatement());

            $this->assertIsArray($this->database::queryLog());
        }

    }

}
