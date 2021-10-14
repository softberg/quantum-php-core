<?php

namespace Quantum\Models {

    use Quantum\Mvc\QtModel;

    class TestModel extends QtModel
    {
        public $table = 'test';
    }

}

namespace Quantum\Test\Unit {

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
            $model = (new ModelFactory)->get(TestModel::class);

            $this->assertInstanceOf(QtModel::class, $model);

            $this->assertInstanceOf(TestModel::class, $model);
        }

        public function testModelNotFound()
        {
            $this->expectException(ModelException::class);

            $this->expectExceptionMessage('Model `NonExistentClass` not found');

            (new ModelFactory)->get(\NonExistentClass::class);
        }

        public function testModelNotInstanceOfQtModel()
        {
            $this->expectException(ModelException::class);

            $this->expectExceptionMessage('Model `Mockery\Undefined` is not instance of `Quantum\Mvc\QtModel`');

            (new ModelFactory)->get(\Mockery\Undefined::class);
        }

    }

}