<?php

namespace Quantum\Tests\Unit\Model\Factories;

use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Model\Exceptions\ModelException;
use Quantum\Tests\_root\shared\Models\User;
use Quantum\Model\Factories\ModelFactory;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Model\QtModel;

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

    public function testModelFactoryGet()
    {
        $userModel = ModelFactory::get(User::class);

        $this->assertInstanceOf(QtModel::class, $userModel);

        $this->assertInstanceOf(User::class, $userModel);
    }

    public function testModelFactoryGetNonExistingModel()
    {
        $this->expectException(ModelException::class);

        $this->expectExceptionMessage('model_not_found');

        ModelFactory::get(\NonExistentClass::class);
    }

    public function testModelFactoryModelNotInstanceOfQtModel()
    {
        $this->expectException(ModelException::class);

        $this->expectExceptionMessage('not_instance_of_model');

        ModelFactory::get(\Mockery\Undefined::class);
    }

    public function testModelFactoryCreateOrmInstance()
    {
        $userModel = ModelFactory::createOrmInstance('user');

        $this->assertInstanceOf(DbalInterface::class, $userModel);
    }

    public function testModelFactoryCreateDynamicModel()
    {
        $dynamicModel = ModelFactory::createDynamicModel('test_table');

        $this->assertInstanceOf(QtModel::class, $dynamicModel);

        $this->assertStringContainsString('@anonymous', get_class($dynamicModel));

        $ormInstance = $this->getPrivateProperty($dynamicModel, 'ormInstance');

        $this->assertInstanceOf(DbalInterface::class, $ormInstance);
    }
}
