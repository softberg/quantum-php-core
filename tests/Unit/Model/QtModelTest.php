<?php


namespace Quantum\Tests\Unit\Model;

use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;
use Quantum\Tests\_root\shared\Models\Profile;
use Quantum\Model\Exceptions\ModelException;
use Quantum\Model\Factories\ModelFactory;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Model\ModelCollection;
use Quantum\Paginator\Paginator;
use Quantum\Model\QtModel;

class QtModelTest extends AppTestCase
{

    private $model;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('debug', true);

        IdiormDbal::connect(['driver' => 'sqlite', 'database' => ':memory:']);

        $this->_createProfileTableWithData();

        $this->model = ModelFactory::get(Profile::class);
    }

    public function tearDown(): void
    {
        IdiormDbal::execute("DROP TABLE profiles");
    }

    public function testModelInstance()
    {
        $this->assertInstanceOf(QtModel::class, $this->model);
    }

    public function testQtModelGetReturnsModelCollection()
    {
        $collection = $this->model->get();

        $this->assertInstanceOf(ModelCollection::class, $collection);

        $this->assertNotEmpty($collection);

        $this->assertInstanceOf(Profile::class, $collection->first());
    }

    public function testQtModelPaginateReturnsPaginator()
    {
        $paginator = $this->model->paginate(1);

        $this->assertInstanceOf(Paginator::class, $paginator);

        $collection = $paginator->data();

        $this->assertInstanceOf(ModelCollection::class, $collection);

        $this->assertCount(1, $collection);

        $this->assertInstanceOf(Profile::class, $collection->first());
    }

    public function testQtModelIsEmptyReturnsFalse()
    {
        $record = $this->model->first();

        $this->assertFalse($record->isEmpty());
    }

    public function testQtModelIsEmptyReturnsTrue()
    {
        $this->model->deleteMany();

        $record = $this->model->first();

        $this->assertTrue($record->isEmpty());
    }

    public function testQtModelWrapToModelReturnsModelInstance()
    {
        $dbalOrmInstance = ModelFactory::createOrmInstance('profiles');

        $profileModel = wrapToModel($dbalOrmInstance, Profile::class);

        $this->assertInstanceOf(Profile::class, $profileModel);
    }

    public function testQtModelFillObjectProps()
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

    public function testQtModelFillObjectPropsWithUndefinedFillable()
    {
        $this->expectException(ModelException::class);

        $this->expectExceptionMessage('inappropriate_property');

        $this->model->fillObjectProps(['country' => 'Ireland']);
    }

    public function testQtModelSetterAndGetter()
    {
        $this->assertNull($this->model->undefinedProperty);

        $this->model->undefinedProperty = 'Something';

        $this->assertEquals('Something', $this->model->undefinedProperty);
    }

    public function testQtModelCallingUndefinedModelMethod()
    {
        $this->expectException(ModelException::class);

        $this->expectExceptionMessage('undefined_model_method');

        $this->model->undefinedMethod();
    }

    public function testQtModelCreateNewRecordByCallingOrmMethod()
    {
        $userModel = $this->model->create();

        $userModel->firstname = 'Jane';

        $userModel->lastname = 'Smith';

        $userModel->age = 31;

        $userModel->save();

        $userId = $userModel->id;

        $userModel = $this->model->findOne($userId);

        $this->assertEquals('Smith', $userModel->lastname);

        $this->assertEquals('Smith', $userModel->prop('lastname'));

        $userData = $userModel->asArray();

        $this->assertEquals('Smith', $userData['lastname']);
    }

    public function testQtModelUpdatingExistingRecordByCallingOrmMethod()
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

        $this->assertEquals('Due', $userModel->lastname);

        $this->assertEquals(35, $userModel->age);
    }

    public function testQtModelCallingModelWithCriterias()
    {
        $profileModel = ModelFactory::get(Profile::class);

        $user = $profileModel->criteria('age', '<', '50')->orderBy('age', 'asc')->first();

        $this->assertIsObject($user);

        $this->assertEquals('Jane', $user->firstname);

        $this->assertEquals('Jane', $user->prop('firstname'));

        $userData = $user->asArray();

        $this->assertIsArray($userData);

        $this->assertEquals('Jane', $userData['firstname']);
    }

    public function testQtModelGetModelProperties()
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

    private function _createProfileTableWithData()
    {
        IdiormDbal::execute("CREATE TABLE IF NOT EXISTS profiles (
                        id INTEGER PRIMARY KEY,
                        password VAARCHAR(255),
                        firstname VARCHAR(255),
                        lastname VARCHAR(255),
                        age int(11)
                    )");

        IdiormDbal::execute("INSERT INTO 
                    profiles
                        (password, firstname, lastname, age) 
                    VALUES
                        ('@R45sdfFD7dsf&', 'John', 'Doe', 45),
                        ('@RaTRdfF9dsa*', 'Jane', 'Dous', 35)
                    ");
    }
}
