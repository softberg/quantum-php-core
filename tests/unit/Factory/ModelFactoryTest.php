<?php

namespace Quantum\Models {

    use Quantum\Mvc\QtModel;

    class TestModel extends QtModel
    {
        public $table = 'test';
    }

}

namespace Quantum\Test\Unit {

    use Mockery;
    use PHPUnit\Framework\TestCase;
    use Quantum\Factory\ModelFactory;
    use Quantum\Exceptions\ModelException;
    use Quantum\Libraries\Database\Database;
    use Quantum\Libraries\Database\IdiormDbal;
    use Quantum\Libraries\Storage\FileSystem;
    use Quantum\Loader\Loader;
    use Quantum\Models\TestModel;
    use Quantum\Di\Di;

    /**
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    class ModelFactoryTest extends TestCase
    {

        private $modelFactory;

        private $dbConfigs = [
            'current' => 'sqlite',
            'sqlite' => array(
                'driver' => 'sqlite',
                'database' => ':memory:'
            ),
        ];

        public function setUp(): void
        {
            (new idiormDbal('test'))->execute("CREATE TABLE profiles (
                        id INTEGER PRIMARY KEY
                    )");

            $loader = new Loader(new FileSystem);

            $loader->loadFile(dirname(__DIR__, 3) . DS . 'src' . DS . 'constants.php');

            $loader->loadDir(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers' . DS . 'functions');

            $loaderMock = Mockery::mock('Quantum\Loader\Loader');

            $loaderMock->shouldReceive('setup')->andReturn($loaderMock);

            $loaderMock->shouldReceive('load')->andReturn($this->dbConfigs);

            $db = Database::getInstance($loaderMock);

            $db->getORM('test');

            Di::loadDefinitions();

            $this->modelFactory = new ModelFactory();
        }

        public function testModelGet()
        {
            $model = $this->modelFactory->get(TestModel::class);

            $this->assertInstanceOf('Quantum\Mvc\QtModel', $model);

            $this->assertInstanceOf('Quantum\Models\TestModel', $model);
        }

        public function testModelNotFound()
        {
            $this->expectException(ModelException::class);

            $this->expectExceptionMessage('Model `NonExistentClass` not found');

            $this->modelFactory->get(\NonExistentClass::class);
        }

        public function testModelNotInstanceOfQtModel()
        {
            $this->expectException(ModelException::class);

            $this->expectExceptionMessage('Model `Mockery\Undefined` is not instance of `Quantum\Mvc\QtModel`');

            $this->modelFactory->get(\Mockery\Undefined::class);
        }

    }

}