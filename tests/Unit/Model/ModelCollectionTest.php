<?php

namespace Quantum\Tests\Unit\Model;

use Quantum\Tests\_root\shared\Models\TestUserModel;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Model\ModelCollection;
use InvalidArgumentException;
use stdClass;

class ModelCollectionTest extends AppTestCase
{
    private $modelCollection;
    private $model1;
    private $model2;

    public function setUp(): void
    {
        parent::setUp();

        $this->modelCollection = new ModelCollection();
        $this->model1 = new TestUserModel();
        $this->model2 = new TestUserModel();
    }

    public function testModelCollectionConstructorWithValidModels()
    {
        $modelCollection = new ModelCollection([$this->model1, $this->model2]);

        $this->assertCount(2, $modelCollection);
    }

    public function testModelCollectionConstructorWithInvalidModels()
    {
        $this->expectException(InvalidArgumentException::class);

        new ModelCollection([new stdClass(), $this->model1, $this->model2]);
    }

    public function testModelCollectionAddModel()
    {
        $this->modelCollection->add($this->model1);
        $this->assertCount(1, $this->modelCollection);
        $this->assertSame($this->model1, $this->modelCollection->first());

        $this->modelCollection->add($this->model2);
        $this->assertCount(2, $this->modelCollection);
        $this->assertSame($this->model2, $this->modelCollection->last());
    }

    public function testModelCollectionRemoveModel()
    {
        $this->modelCollection->add($this->model1);
        $this->modelCollection->add($this->model2);

        $this->modelCollection->remove($this->model1);

        $this->assertCount(1, $this->modelCollection);
        $this->assertSame($this->model2, $this->modelCollection->first());
    }

    public function testModelCollectionAllModels()
    {
        $this->modelCollection->add($this->model1);
        $this->modelCollection->add($this->model2);

        $models = $this->modelCollection->all();

        $this->assertCount(2, $models);
        $this->assertSame($this->model1, $models[0]);
        $this->assertSame($this->model2, $models[1]);
    }

    public function testModelCollectionFirstModel()
    {
        $this->modelCollection->add($this->model1);
        $this->modelCollection->add($this->model2);

        $this->assertSame($this->model1, $this->modelCollection->first());
    }

    public function testModelCollectionLastModel()
    {
        $this->modelCollection->add($this->model1);
        $this->modelCollection->add($this->model2);

        $this->assertSame($this->model2, $this->modelCollection->last());
    }

    public function testModelCollectionIsEmpty()
    {
        $this->assertTrue($this->modelCollection->isEmpty());

        $this->modelCollection->add($this->model1);
        $this->assertFalse($this->modelCollection->isEmpty());
    }

    public function testModelCollectionCount()
    {
        $this->assertCount(0, $this->modelCollection);

        $this->modelCollection->add($this->model1);
        $this->assertCount(1, $this->modelCollection);

        $this->modelCollection->add($this->model2);
        $this->assertCount(2, $this->modelCollection);
    }

    public function testModelCollectionGetIterator()
    {
        $this->modelCollection->add($this->model1);
        $this->modelCollection->add($this->model2);

        $models = iterator_to_array($this->modelCollection->getIterator());

        $this->assertCount(2, $models);
        $this->assertSame($this->model1, $models[0]);
        $this->assertSame($this->model2, $models[1]);
    }
}