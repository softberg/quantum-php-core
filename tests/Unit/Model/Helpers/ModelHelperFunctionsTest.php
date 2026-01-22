<?php

namespace Quantum\Tests\Unit\Model\Helpers;

use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Tests\_root\shared\Models\TestUserModel;
use Quantum\Model\Exceptions\ModelException;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Model\DbModel;
use Mockery;

class ModelHelperFunctionsTest extends AppTestCase
{
    public function testModelReturnsDbModelInstance()
    {
        $model = model(TestUserModel::class);

        $this->assertInstanceOf(TestUserModel::class, $model);

        $this->assertInstanceOf(DbModel::class, $model);
    }

    public function testModelThrowsOnInvalidClass()
    {
        $this->expectException(ModelException::class);

        model('NonExistentModelClass');
    }

    public function testDynamicModelReturnsAnonymousDbModel()
    {
        $dynamicModel = dynamicModel('test_table', TestUserModel::class);

        $this->assertInstanceOf(DbModel::class, $dynamicModel);

        $this->assertStringContainsString('@anonymous', get_class($dynamicModel));

        $this->assertEquals(TestUserModel::class, $dynamicModel->getModelName());
    }

    public function testWrapToModelReturnsModelInstance()
    {
        $dbal = Mockery::mock(DbalInterface::class);

        $dbal->shouldReceive('asArray')->andReturn([]);

        $model = wrapToModel($dbal, TestUserModel::class);

        $this->assertInstanceOf(DbModel::class, $model);
    }

    public function testWrapToModelThrowsOnInvalidClass()
    {
        $dbal = Mockery::mock(DbalInterface::class);

        $this->expectException(ModelException::class);

        wrapToModel($dbal, 'NonExistentModelClass');
    }

    public function testWrapToModelThrowsIfNotDbModel()
    {
        $dbal = Mockery::mock(DbalInterface::class);

        $this->expectException(ModelException::class);

        wrapToModel($dbal, \stdClass::class);
    }
}
