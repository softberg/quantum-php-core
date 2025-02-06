<?php

namespace Quantum\Models {

    use Quantum\Mvc\QtModel;

    class ProfileModel extends QtModel
    {

        public $table = 'profiles';

        protected $fillable = [
            'password',
            'firstname',
            'lastname',
            'age'
        ];

        public $hidden = [
            'password'
        ];
    }
}

namespace Quantum\Tests\Unit\Mvc {

    use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;
    use Quantum\Libraries\Database\Exceptions\ModelException;
    use Quantum\Factory\ModelFactory;
    use Quantum\Models\ProfileModel;
    use Quantum\Tests\Unit\AppTestCase;
    use Quantum\Mvc\QtModel;

    class QtModelTest extends AppTestCase
    {

        private $model;

        public function setUp(): void
        {
            parent::setUp();

            config()->set('debug', true);

            IdiormDbal::connect(['driver' => 'sqlite', 'database' => ':memory:']);

            IdiormDbal::execute("CREATE TABLE profiles (
                        id INTEGER PRIMARY KEY,
                        password VAARCHAR(255),
                        firstname VARCHAR(255),
                        lastname VARCHAR(255),
                        age int(11)
                    )");

            IdiormDbal::execute("INSERT INTO 
                    profiles
                        (
                            password,
                            firstname,
                            lastname,
                            age
                            ) 
                    VALUES
                        (
                            '@R45sdfFD7dsf&',
                            'John',
                            'Doe',
                            45
                            ),
                        (
                            '@RaTRdfF9dsa*',
                            'Jane',
                            'Dous',
                            35
                            )
                    ");


            $this->model = ModelFactory::get(ProfileModel::class);
        }

        public function tearDown(): void
        {
            IdiormDbal::execute("DROP TABLE profiles");
        }

        public function testModelInstance()
        {
            $this->assertInstanceOf(QtModel::class, $this->model);
        }

        public function testFillObjectProps()
        {
            $this->model->fillObjectProps([
                'firstname' => 'Jane',
                'lastname' => 'Due',
                'age' => 35
            ]);

            $this->assertEquals('Jane', $this->model->firstname);

            $this->assertEquals('Due', $this->model->lastname);

            $this->assertEquals(35, $this->model->age);
        }

        public function testFillObjectPropsWithUndefinedFillable()
        {
            $this->expectException(ModelException::class);

            $this->expectExceptionMessage('inappropriate_property');

            $this->model->fillObjectProps(['country' => 'Ireland']);
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

            $this->expectExceptionMessage('undefined_model_method');

            $this->model->undefinedMethod();
        }

        public function testCreateNewRecordByCallingOrmMethod()
        {
            $userModel = $this->model->create();

            $userModel->firstname = 'Jane';

            $userModel->lastname = 'Due';

            $userModel->age = 35;

            $userModel->save();

            $userModel = $this->model->findOne(1);

            $this->assertEquals('John', $userModel->firstname);

            $this->assertEquals('John', $userModel->prop('firstname'));

            $userData = $userModel->asArray();

            $this->assertEquals('John', $userData['firstname']);
        }

        public function testUpdatingExistingRecordByCallingOrmMethod()
        {
            $userModel = $this->model->findOne(1);

            $this->assertEquals('John', $userModel->firstname);

            $this->assertEquals('Doe', $userModel->lastname);

            $this->assertEquals(45, $userModel->age);

            $userModel->firstname = 'Jane';

            $userModel->lastname = 'Due';

            $userModel->age = 35;

            $userModel->save();

            $userModel = $this->model->findOne(1);

            $this->assertEquals('Jane', $userModel->firstname);

            $this->assertEquals('Jane', $userModel->prop('firstname'));

            $userData = $userModel->asArray();

            $this->assertEquals('Jane', $userData['firstname']);
        }

        public function testCallingModelWithCriterias()
        {
            $userProfile = $this->model->create();

            $userProfile->fillObjectProps([
                'firstname' => 'Jane',
                'lastname' => 'Du',
                'age' => 35
            ]);

            $userProfile->save();

            $profileModel = ModelFactory::get(ProfileModel::class);

            $users = $profileModel->criteria('age', '>', 30)->get();

            $this->assertIsArray($users);

            $this->assertCount(3, $users);

            $profileModel = ModelFactory::get(ProfileModel::class);

            $user = $profileModel->criteria('age', '<', '50')->orderBy('age', 'asc')->first();

            $this->assertIsObject($user);

            $this->assertEquals('Jane', $user->firstname);

            $this->assertEquals('Jane', $user->prop('firstname'));

            $userData = $user->asArray();

            $this->assertIsArray($userData);

            $this->assertEquals('Jane', $userData['firstname']);
        }

        public function testGetModelProperties()
        {
            $expected = [
                "id" => "1",
                "firstname" => "John",
                "lastname" => "Doe",
                "age" => "45"
            ];

            $actual = $this->model->orderBy('id', 'asc')->first()->asArray();

            $this->assertIsArray($actual);

            $this->assertArrayNotHasKey('password', $actual);

            $this->assertEquals($expected, $actual);
        }
    }
}