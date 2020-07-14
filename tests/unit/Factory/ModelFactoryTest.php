<?php

namespace Quantum\Models {

    use Quantum\Mvc\QtModel;

    class TestModel extends QtModel {}

}

namespace Quantum\Test\Unit {

    use Mockery;
    use PHPUnit\Framework\TestCase;
    use Quantum\Factory\ModelFactory;
    use Quantum\Exceptions\ModelException;
    use Quantum\Models\TestModel;

    /**
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    class ModelFactoryTest extends TestCase
    {

        private $modelFactory;
        private $databaseMock;
        private $helperMock;

        public function setUp(): void
        {
            $this->databaseMock = Mockery::mock('overload:Quantum\Libraries\Database\Database');

            $this->databaseMock->shouldReceive('getORM')->andReturn(new \stdClass());

            $this->helperMock = Mockery::mock('overload:Quantum\Helpers\Helper');

            $this->helperMock->shouldReceive('_message')->andReturnUsing(function($subject, $params) {
                if (is_array($params)) {
                    return preg_replace_callback('/{%\d+}/', function () use (&$params) {
                        return array_shift($params);
                    }, $subject);
                } else {
                    return preg_replace('/{%\d+}/', $params, $subject);
                }
            });

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