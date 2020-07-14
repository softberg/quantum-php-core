<?php

namespace Quantum\Models {

    use Quantum\Mvc\QtModel;

    class StubModel extends QtModel
    {

        public $table = 'test';
        protected $fillable = [
            'firstname',
            'lastname'
        ];

    }

}

namespace Quantum\Test\Unit {

    use Mockery;
    use PHPUnit\Framework\TestCase;
    use Quantum\Models\StubModel;
    use Quantum\Exceptions\ModelException;

    /**
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    class QtModelTest extends TestCase
    {

        private $model;
        private $testObject = [
            'firstname' => 'John',
            'lastname' => 'Doe'
        ];

        public function setUp(): void
        {
            $dbal = Mockery::mock();
            
            $dbal->ormObject = Mockery::mock();

            $this->databaseMock = Mockery::mock('overload:Quantum\Libraries\Database\Database');

            $this->databaseMock->shouldReceive('getORM')->andReturn($dbal);

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

            $this->model = new StubModel();
        }

        public function tearDown(): void
        {
            Mockery::close();
        }

        public function testModelInstance()
        {
            $this->assertInstanceOf('Quantum\Mvc\QtModel', $this->model);
        }

        public function testFillObjectProps()
        {
            $this->model->fillObjectProps($this->testObject);

            $this->assertEquals('John', $this->model->firstname);

            $this->assertEquals('Doe', $this->model->lastname);
        }

        public function testFillObjectPropsWithUndefinedFillable()
        {
            $this->expectException(ModelException::class);

            $this->expectExceptionMessage('Inappropriate property `age` for fillable object');

            $this->model->fillObjectProps(['age' => 30]);
        }

        public function testSetterAndGetter()
        {
            $this->assertNull($this->model->undefinedProperty);

            $this->model->undefinedProperty = 'Something';

            $this->assertEquals('Something', $this->model->undefinedProperty);
        }

    }

}