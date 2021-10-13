<?php

namespace Quantum\Models {

    use Quantum\Mvc\QtModel;

    class ProfileModel extends QtModel
    {

        public $table = 'profiles';
        protected $fillable = [
            'firstname',
            'lastname'
        ];

    }

}

namespace Quantum\Test\Unit {

    use PHPUnit\Framework\TestCase;
    use Quantum\Libraries\Database\Idiorm\IdiormDbal;
    use Quantum\Exceptions\ModelException;
    use Quantum\Factory\ModelFactory;
    use Quantum\Models\ProfileModel;
    use Quantum\Mvc\QtModel;
    use Quantum\Di\Di;
    use Quantum\App;

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

            App::loadCoreFunctions(dirname(__DIR__, 3) . DS . 'src' . DS . 'Helpers');

            App::setBaseDir(dirname(__DIR__) . DS . '_root');

            Di::loadDefinitions();

            IdiormDbal::execute("CREATE TABLE profiles (
                        id INTEGER PRIMARY KEY,
                        firstname VARCHAR(255),
                        lastname VARCHAR(255),
                        created_at DATETIME
                    )");

            $this->model = (new ModelFactory)->get(ProfileModel::class);
        }

        public function testModelInstance()
        {
            $this->assertInstanceOf(QtModel::class, $this->model);
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

        public function testCallingUndefinedModelMethod()
        {
            $this->expectException(ModelException::class);

            $this->expectExceptionMessage('Model method `undefinedMethod` is not defined');

            $this->model->undefinedMethod();
        }

        public function testCallingModelMethod()
        {
            $userProfile = $this->model->findOne(1)->asArray();

            $this->assertIsArray($userProfile);

            $this->assertEmpty($userProfile);

            $userProfile = $this->model->create();

            $userProfile->fillObjectProps($this->testObject);

            $userProfile->save();

            $userProfile = $this->model->findOne(1)->asArray();

            $this->assertIsArray($userProfile);

            $this->assertNotEmpty($userProfile);

            $this->assertEquals('John', $userProfile['firstname']);
        }

    }

}