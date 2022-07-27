<?php

namespace Quantum\Models {

    use Quantum\Mvc\QtModel;

    class TestModel extends QtModel
    {
        public $table = 'test';
    }

}

namespace Quantum\Tests\Factory {

    use PHPUnit\Framework\TestCase;
    use Quantum\Exceptions\ModelException;
    use Quantum\Factory\ModelFactory;
    use Quantum\Models\TestModel;
    use Quantum\Mvc\QtModel;
    use Quantum\Di\Di;
    use Quantum\App;

    /**
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    class ModelFactoryTest extends TestCase
    {

        public function setUp(): void
        {
            App::loadCoreFunctions(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers');

            App::setBaseDir(dirname(__DIR__) . DS . '_root');

            Di::loadDefinitions();
        }

        public function testModelGet()
        {
            $model = ModelFactory::get(TestModel::class);

            $this->assertInstanceOf(QtModel::class, $model);

            $this->assertInstanceOf(TestModel::class, $model);
        }

        public function testModelNotFound()
        {
            $this->expectException(ModelException::class);

            $this->expectExceptionMessage('model_not_found');

            ModelFactory::get(\NonExistentClass::class);
        }

        public function testModelNotInstanceOfQtModel()
        {
            $this->expectException(ModelException::class);

            $this->expectExceptionMessage('not_instance_of_model');

            ModelFactory::get(\Mockery\Undefined::class);
        }

    }

}