<?php

namespace Quantum\Tests\Unit\Model\Helpers;

use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Model\Exceptions\ModelException;
use Quantum\Tests\_root\shared\Models\User;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Model\QtModel;
use Mockery;

class ModelHelperFunctionsTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testModelReturnsQtModelInstance()
    {
        $model = model(User::class);

        $this->assertInstanceOf(User::class, $model);

        $this->assertInstanceOf(QtModel::class, $model);
    }

    public function testModelThrowsOnInvalidClass()
    {
        $this->expectException(ModelException::class);

        model('NonExistentModelClass');
    }

    public function testDynamicModelReturnsAnonymousQtModel()
    {
        $dynamic = dynamicModel('test_table');

        $this->assertInstanceOf(QtModel::class, $dynamic);

        $this->assertStringContainsString('@anonymous', get_class($dynamic));
    }

    public function testWrapToModelReturnsModelInstance()
    {
        $dbal = Mockery::mock(DbalInterface::class);

        $model = wrapToModel($dbal, User::class);

        $this->assertInstanceOf(QtModel::class, $model);
    }

    public function testWrapToModelThrowsOnInvalidClass()
    {
        $dbal = Mockery::mock(DbalInterface::class);

        $this->expectException(\InvalidArgumentException::class);

        wrapToModel($dbal, 'NonExistentModelClass');
    }

    public function testWrapToModelThrowsIfNotQtModel()
    {
        $dbal = Mockery::mock(DbalInterface::class);

        $this->expectException(\InvalidArgumentException::class);

        wrapToModel($dbal, \stdClass::class);
    }
} 