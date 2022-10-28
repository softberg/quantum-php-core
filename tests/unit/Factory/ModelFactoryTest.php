<?php

namespace Quantum\Models {

    use Quantum\Mvc\QtModel;

    class TestModel extends QtModel
    {

        public $table = 'test';

    }

}

namespace Quantum\Tests\Factory {

    use Quantum\Exceptions\ModelException;
    use Quantum\Factory\ModelFactory;
    use Quantum\Tests\AppTestCase;
    use Quantum\Models\TestModel;
    use Quantum\Mvc\QtModel;

    /**
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    class ModelFactoryTest extends AppTestCase
    {

        public function setUp(): void
        {
            parent::setUp();
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