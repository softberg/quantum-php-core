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
    use Quantum\Libraries\Storage\FileSystem;
    use Quantum\Loader\Loader;

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

            $loader = new Loader(new FileSystem);

            $loader->loadDir(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers' . DS . 'functions');

            $dbal = Mockery::mock();

            $dbal->ormObject = Mockery::mock();

            $this->databaseMock = Mockery::mock('overload:Quantum\Libraries\Database\Database');

            $this->databaseMock->shouldReceive('getORM')->andReturn($dbal);

            $this->helperMock = Mockery::mock('overload:Quantum\Helpers\Helper');
            
            $this->model = (new \Quantum\Factory\ModelFactory)->get(StubModel::class);
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