<?php

namespace Quantum\Tests\Unit\Model;

use Quantum\Libraries\Database\Adapters\Sleekdb\SleekDbal;
use Quantum\Tests\_root\shared\Models\TestProductsModel;
use Quantum\Model\Factories\ModelFactory;
use Quantum\Libraries\Database\Database;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Model\ModelCollection;
use Quantum\Paginator\Paginator;
use Quantum\Loader\Setup;

class ModelSoftDeletesSleekTest extends AppTestCase
{
    private $model;

    public function setUp(): void
    {
        parent::setUp();

        $this->setPrivateProperty(Database::class, 'instance', null);

        if (!config()->has('database')) {
            config()->import(new Setup('config', 'database'));
        }

        config()->set('database.default', 'sleekdb');

        SleekDbal::connect(config()->get('database.sleekdb'));

        $this->_createProductsTableWithData();

        $this->model = ModelFactory::get(TestProductsModel::class);
    }

    public function tearDown(): void
    {
        ModelFactory::get(TestProductsModel::class)->truncate();
    }

    public function testSleekDeleteSetsDeletedAt()
    {
        $this->assertEquals(4, $this->model->count());

        $product = $this->model->findOne(1);

        $this->assertNull($product->prop('deleted_at'));

        $product->delete();

        $this->assertNotNull($product->prop('deleted_at'));

        $this->assertEquals(3, $this->model->count());
    }

    public function testSleekRestoreSetsDeletedAtToNull()
    {
        $this->assertEquals(4, $this->model->count());

        $product = $this->model->findOne(1);

        $product->delete();

        $this->assertNotNull($product->prop('deleted_at'));

        $this->assertEquals(3, $this->model->count());

        $product->restore();

        $this->assertNull($product->prop('deleted_at'));

        $this->assertEquals(4, $this->model->count());
    }

    public function testSleekForceDeleteActuallyDeletes()
    {
        $this->assertEquals(6, $this->model->withTrashed()->count());

        $product = $this->model->findOne(1);

        $this->assertFalse($product->isEmpty());

        $product->forceDelete();

        $this->assertEquals(5, $this->model->withTrashed()->count());

        $product = $this->model->findOne(1);

        $this->assertTrue($product->isEmpty());
    }

    public function testSleekGetReturnsOnlyNonDeletedRecords()
    {
        $result = $this->model->get();

        $this->assertInstanceOf(ModelCollection::class, $result);

        $this->assertCount(4, $result);
    }

    public function testSleekGetWithTrashedReturnsAllRecords()
    {
        $result = $this->model->withTrashed()->get();

        $this->assertInstanceOf(ModelCollection::class, $result);

        $this->assertCount(6, $result);
    }

    public function testSleekGetOnlyTrashedReturnsOnlySoftDeletedRecords()
    {
        $result = $this->model->onlyTrashed()->get();

        $this->assertInstanceOf(ModelCollection::class, $result);

        $this->assertCount(2, $result);
    }

    public function testSleekPaginateExcludesDeleted()
    {
        $paginator = $this->model->paginate(3);

        $this->assertInstanceOf(Paginator::class, $paginator);

        foreach ($paginator->data() as $product) {

            $this->assertNull($product->prop('deleted_at'));
        }
    }

    public function testSleekCountExcludesDeleted()
    {
        $this->assertEquals(4, $this->model->count());

        $this->assertEquals(6, ModelFactory::get(TestProductsModel::class)->withTrashed()->count());
    }

    public function testSleekFindOneRespectsSoftDelete()
    {
        $product = $this->model->findOne(2);

        $this->assertTrue($product->isEmpty());

        $product = $this->model->withTrashed()->findOne(2);

        $this->assertFalse($product->isEmpty());

        $this->assertEquals('Product B', $product->prop('title'));
    }

    public function testSleekFindOneByRespectsSoftDelete()
    {
        $product = $this->model->findOneBy('title', 'Product B');

        $this->assertTrue($product->isEmpty());

        $product = $this->model->withTrashed()->findOneBy('title', 'Product B');

        $this->assertFalse($product->isEmpty());
    }

    public function testSleekFirstRespectsSoftDelete()
    {
        $product = $this->model->criteria('title', '=', 'Product B')->first();

        $this->assertTrue($product->isEmpty());

        $this->assertNull($product->prop('deleted_at'));

        $product = $this->model->withTrashed()->criteria('title', '=', 'Product B')->first();

        $this->assertFalse($product->isEmpty());
    }

    private function _createProductsTableWithData()
    {
        $product = new SleekDbal('products');

        $product->create();
        $product->prop('title', 'Product A');
        $product->prop('description', 'High-quality product A');
        $product->prop('price', 19.99);
        $product->prop('created_at', '2025-05-01 10:00:00');
        $product->prop('deleted_at', null);
        $product->save();

        $product->create();
        $product->prop('title', 'Product B');
        $product->prop('description', 'Eco-friendly product B');
        $product->prop('price', 29.99);
        $product->prop('created_at', '2025-05-01 10:00:00');
        $product->prop('deleted_at', '2025-05-11 08:00:00');
        $product->save();

        $product->create();
        $product->prop('title', 'Product C');
        $product->prop('description', 'Popular product C');
        $product->prop('price', 39.99);
        $product->prop('created_at', '2025-05-01 10:00:00');
        $product->prop('deleted_at', null);
        $product->save();

        $product->create();
        $product->prop('title', 'Product D');
        $product->prop('description', 'Limited edition product D');
        $product->prop('price', 49.99);
        $product->prop('created_at', '2025-05-01 10:00:00');
        $product->prop('deleted_at', null);
        $product->save();

        $product->create();
        $product->prop('title', 'Product E');
        $product->prop('description', 'Budget-friendly product E');
        $product->prop('price', 59.99);
        $product->prop('created_at', '2025-05-01 10:00:00');
        $product->prop('deleted_at', '2025-05-11 08:00:00');
        $product->save();

        $product->create();
        $product->prop('title', 'Product F');
        $product->prop('description', 'Premium product F');
        $product->prop('price', 69.99);
        $product->prop('created_at', '2025-05-01 10:00:00');
        $product->prop('deleted_at', null);
        $product->save();
    }
}
