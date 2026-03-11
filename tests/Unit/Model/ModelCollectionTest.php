<?php

namespace Quantum\Tests\Unit\Model;

use Quantum\Tests\_root\shared\Models\TestUserModel;
use Quantum\Model\Exceptions\ModelException;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Model\ModelCollection;
use stdClass;

class ModelCollectionTest extends AppTestCase
{
    private ModelCollection $modelCollection;
    private TestUserModel $model1;
    private TestUserModel $model2;

    public function setUp(): void
    {
        parent::setUp();

        $this->modelCollection = new ModelCollection();
        $this->model1 = new TestUserModel();
        $this->model2 = new TestUserModel();
    }

    public function testModelCollectionConstructorWithValidModels(): void
    {
        $modelCollection = new ModelCollection([$this->model1, $this->model2]);

        $this->assertCount(2, $modelCollection);
    }

    public function testModelCollectionConstructorWithInvalidModels(): void
    {
        $this->expectException(ModelException::class);

        new ModelCollection([new stdClass(), $this->model1, $this->model2]);
    }

    public function testModelCollectionAddModel(): void
    {
        $this->modelCollection->add($this->model1);
        $this->assertCount(1, $this->modelCollection);
        $this->assertSame($this->model1, $this->modelCollection->first());

        $this->modelCollection->add($this->model2);
        $this->assertCount(2, $this->modelCollection);
        $this->assertSame($this->model2, $this->modelCollection->last());
    }

    public function testModelCollectionRemoveModel(): void
    {
        $this->modelCollection->add($this->model1);
        $this->modelCollection->add($this->model2);

        $this->modelCollection->remove($this->model1);

        $this->assertCount(1, $this->modelCollection);
        $this->assertSame($this->model2, $this->modelCollection->first());
    }

    public function testModelCollectionAllModels(): void
    {
        $this->modelCollection->add($this->model1);
        $this->modelCollection->add($this->model2);

        $models = $this->modelCollection->all();

        $this->assertCount(2, $models);
        $this->assertSame($this->model1, $models[0]);
        $this->assertSame($this->model2, $models[1]);
    }

    public function testModelCollectionFirstModel(): void
    {
        $this->modelCollection->add($this->model1);
        $this->modelCollection->add($this->model2);

        $this->assertSame($this->model1, $this->modelCollection->first());
    }

    public function testModelCollectionLastModel(): void
    {
        $this->modelCollection->add($this->model1);
        $this->modelCollection->add($this->model2);

        $this->assertSame($this->model2, $this->modelCollection->last());
    }

    public function testModelCollectionIsEmpty(): void
    {
        $this->assertTrue($this->modelCollection->isEmpty());

        $this->modelCollection->add($this->model1);
        $this->assertFalse($this->modelCollection->isEmpty());
    }

    public function testModelCollectionCount(): void
    {
        $this->assertCount(0, $this->modelCollection);

        $this->modelCollection->add($this->model1);
        $this->assertCount(1, $this->modelCollection);

        $this->modelCollection->add($this->model2);
        $this->assertCount(2, $this->modelCollection);
    }

    public function testModelCollectionGetIterator(): void
    {
        $this->modelCollection->add($this->model1);
        $this->modelCollection->add($this->model2);

        $models = iterator_to_array($this->modelCollection->getIterator());

        $this->assertCount(2, $models);
        $this->assertSame($this->model1, $models[0]);
        $this->assertSame($this->model2, $models[1]);
    }
}
