<?php

namespace Quantum\Tests\Unit\Model;

use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;
use Quantum\Tests\_root\shared\Models\TestProfileModel;
use Quantum\Model\Exceptions\ModelException;
use Quantum\Model\Factories\ModelFactory;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Model\ModelCollection;
use Quantum\Paginator\Paginator;
use Quantum\Model\DbModel;

class DbModelTest extends AppTestCase
{
    private $model;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('app.debug', true);

        IdiormDbal::connect(['driver' => 'sqlite', 'database' => ':memory:']);

        $this->_createProfileTableWithData();

        $this->model = ModelFactory::get(TestProfileModel::class);
    }

    public function tearDown(): void
    {
        IdiormDbal::execute('DROP TABLE profiles');
    }

    public function testDbModelInstance()
    {
        $this->assertInstanceOf(DbModel::class, $this->model);
    }

    public function testDbModelSetAndGetOrmInstance()
    {
        $ormInstance = new IdiormDbal('profiles');

        $this->model->setOrmInstance($ormInstance);

        $this->assertSame($ormInstance, $this->model->getOrmInstance());
    }

    public function testDbModelRelationsReturnsArray()
    {
        $relations = $this->model->relations();

        $this->assertIsArray($relations);
    }

    public function testDbModelGetReturnsModelCollection()
    {
        $collection = $this->model->get();

        $this->assertInstanceOf(ModelCollection::class, $collection);

        $this->assertNotEmpty($collection);

        $this->assertInstanceOf(TestProfileModel::class, $collection->first());
    }

    public function testDbModelPaginateReturnsPaginator()
    {
        $paginator = $this->model->paginate(1);

        $this->assertInstanceOf(Paginator::class, $paginator);

        $collection = $paginator->data();

        $this->assertInstanceOf(ModelCollection::class, $collection);

        $this->assertCount(1, $collection);

        $this->assertInstanceOf(TestProfileModel::class, $collection->first());
    }

    public function testDbModelIsEmptyReturnsFalse()
    {
        $record = $this->model->first();

        $this->assertFalse($record->isEmpty());
    }

    public function testDbModelIsEmptyReturnsTrue()
    {
        $this->model->deleteMany();

        $record = $this->model->first();

        $this->assertTrue($record->isEmpty());
    }

    public function testDbModelFill()
    {
        $this->model->fill([
            'firstname' => 'Jane',
            'lastname' => 'Due',
            'age' => 35,
        ]);

        $this->assertEquals('Jane', $this->model->firstname);

        $this->assertEquals('Due', $this->model->lastname);

        $this->assertEquals(35, $this->model->age);
    }

    public function testDbModelFillWithUndefinedFillable()
    {
        $this->expectException(ModelException::class);

        $this->expectExceptionMessage('Inappropriate property `currency` for fillable object');

        $this->model->fill(['currency' => 'Ireland']);
    }

    public function testDbModelSetterAndGetter()
    {
        $this->assertNull($this->model->undefinedProperty);

        $this->model->undefinedProperty = 'Something';

        $this->assertEquals('Something', $this->model->undefinedProperty);
    }

    public function testDbModelCallingUndefinedModelMethod()
    {
        $this->expectException(ModelException::class);

        $this->expectExceptionMessage('The method `undefinedMethod` is not supported for `' . IdiormDbal::class . '`');

        $this->model->undefinedMethod();
    }

    public function testDbModelCreateNewRecordByCallingOrmMethod()
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

    public function testDbModelUpdatingExistingRecordByCallingOrmMethod()
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

    public function testDbModelPropUnknownColumnThrowsPdoExceptionOnSave()
    {
        $userModel = $this->model->create();

        $userModel->firstname = 'Bypass';
        $userModel->lastname = 'Test';
        $userModel->age = 20;

        $userModel->currency = 'USD';

        $this->expectException(\PDOException::class);

        $userModel->save();
    }

    public function testDbModelCallingModelWithCriterias()
    {
        $profileModel = ModelFactory::get(TestProfileModel::class);

        $user = $profileModel->criteria('age', '<', '40')->orderBy('age', 'asc')->first();

        $this->assertIsObject($user);

        $this->assertEquals('Jane', $user->firstname);

        $this->assertEquals('Jane', $user->prop('firstname'));

        $userData = $user->asArray();

        $this->assertIsArray($userData);

        $this->assertEquals('Jane', $userData['firstname']);
    }

    public function tesDbModelGetModelProperties()
    {
        $expected = [
            'id' => '1',
            'user_id' => '1',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'age' => '45',
            'country' => 'Ireland',
            'created_at' => '2025-12-17 19:27:46',
        ];

        $profileModel = ModelFactory::get(TestProfileModel::class);

        $actual = $profileModel->orderBy('id', 'asc')->first()->asArray();

        $this->assertIsArray($actual);

        $this->assertEquals($expected, $actual);
    }

    private function _createProfileTableWithData()
    {
        IdiormDbal::execute('CREATE TABLE IF NOT EXISTS profiles (
                        id INTEGER PRIMARY KEY,
                        user_id INTEGER(11),
                        firstname VARCHAR(255),
                        lastname VARCHAR(255),
                        age INTEGER(11),
                        country VARCHAR(255),
                        created_at DATETIME
                    )');

        IdiormDbal::execute("
                    INSERT INTO profiles
                        (user_id, firstname, lastname, age, country, created_at)
                    VALUES
                        (1, 'John', 'Doe', 45, 'Ireland', '2025-12-17 19:27:46'),
                        (2, 'Jane', 'Due', 35, 'England', '2025-12-17 19:27:47')
                ");
    }
}
