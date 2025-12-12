<?php

namespace Quantum\Tests\Unit\Model\Factories;

use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Tests\_root\shared\Models\TestUserModel;
use Quantum\Model\Exceptions\ModelException;
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
        $userModel = ModelFactory::get(TestUserModel::class);

        $this->assertInstanceOf(QtModel::class, $userModel);

        $this->assertInstanceOf(TestUserModel::class, $userModel);
    }

    public function testModelFactoryGetNonExistingModel()
    {
        $this->expectException(ModelException::class);

        $this->expectExceptionMessage('Model `NonExistentClass` not found');

        ModelFactory::get(\NonExistentClass::class);
    }

    public function testModelFactoryModelNotInstanceOfQtModel()
    {
        $this->expectException(ModelException::class);

        $this->expectExceptionMessage('The `Mockery\Undefined` is not instance of `Quantum\Model\QtModel`');

        ModelFactory::get(\Mockery\Undefined::class);
    }

    public function testModelFactoryCreateDynamicModel()
    {
        $dynamicModel = ModelFactory::createDynamicModel('test_table', TestUserModel::class);

        $this->assertInstanceOf(QtModel::class, $dynamicModel);

        $this->assertStringContainsString('@anonymous', get_class($dynamicModel));

        $this->assertEquals(TestUserModel::class, $dynamicModel->getModelName());

        $ormInstance = $this->getPrivateProperty($dynamicModel, 'ormInstance');

        $this->assertInstanceOf(DbalInterface::class, $ormInstance);
    }
}
