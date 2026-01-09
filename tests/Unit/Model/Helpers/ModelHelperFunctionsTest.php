<?php

namespace Quantum\Tests\Unit\Model\Helpers;

use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Tests\_root\shared\Models\TestUserModel;
use Quantum\Model\Exceptions\ModelException;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Model\QtModel;
use Mockery;

class ModelHelperFunctionsTest extends AppTestCase
{
    public function testModelReturnsQtModelInstance()
    {
        $model = model(TestUserModel::class);

        $this->assertInstanceOf(TestUserModel::class, $model);

        $this->assertInstanceOf(QtModel::class, $model);
    }

    public function testModelThrowsOnInvalidClass()
    {
        $this->expectException(ModelException::class);

        model('NonExistentModelClass');
    }

    public function testDynamicModelReturnsAnonymousQtModel()
    {
        $dynamicModel = dynamicModel('test_table', TestUserModel::class);

        $this->assertInstanceOf(QtModel::class, $dynamicModel);

        $this->assertStringContainsString('@anonymous', get_class($dynamicModel));

        $this->assertEquals(TestUserModel::class, $dynamicModel->getModelName());
    }

    public function testWrapToModelReturnsModelInstance()
    {
        $dbal = Mockery::mock(DbalInterface::class);

        $model = wrapToModel($dbal, TestUserModel::class);

        $this->assertInstanceOf(QtModel::class, $model);
    }

    public function testWrapToModelThrowsOnInvalidClass()
    {
        $dbal = Mockery::mock(DbalInterface::class);

        $this->expectException(ModelException::class);

        wrapToModel($dbal, 'NonExistentModelClass');
    }

    public function testWrapToModelThrowsIfNotQtModel()
    {
        $dbal = Mockery::mock(DbalInterface::class);

        $this->expectException(ModelException::class);

        wrapToModel($dbal, \stdClass::class);
    }
}
