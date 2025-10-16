<?php

namespace Quantum\Tests\Unit\Model;

use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;
use Quantum\Tests\_root\shared\Models\TestProductsModel;
use Quantum\Model\Factories\ModelFactory;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Model\ModelCollection;
use Quantum\Paginator\Paginator;

class ModelSoftDeletesIdiOrm extends AppTestCase
{

    private $model;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('app.debug', true);

        IdiormDbal::connect(['driver' => 'sqlite', 'database' => ':memory:']);

        $this->_createProductsTableWithData();

        $this->model = ModelFactory::get(TestProductsModel::class);
    }

    public function tearDown(): void
    {
        IdiormDbal::execute("DROP TABLE products ");
    }

    public function testDeleteSetsDeletedAt()
    {
        $this->assertEquals(4, $this->model->count());

        $product = $this->model->findOne(1);

        $this->assertNull($product->prop('deleted_at'));

        $product->delete();

        $this->assertNotNull($product->prop('deleted_at'));

        $this->assertEquals(3, $this->model->count());
    }

    public function testRestoreSetsDeletedAtToNull()
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

    public function testForceDeleteActuallyDeletes()
    {
        $this->assertEquals(6, $this->model->withTrashed()->count());

        $product = $this->model->findOne(1);

        $product->forceDelete();

        $this->assertEquals(5, $this->model->withTrashed()->count());

        $product = ModelFactory::get(TestProductsModel::class)->findOne(1);

        $this->assertTrue($product->isEmpty());
    }

    public function testGetReturnsOnlyNonDeletedRecords()
    {
        $result = $this->model->get();

        $this->assertInstanceOf(ModelCollection::class, $result);

        $this->assertCount(4, $result);
    }

    public function testGetWithTrashedReturnsAllRecords()
    {
        $result = $this->model->withTrashed()->get();

        $this->assertInstanceOf(ModelCollection::class, $result);

        $this->assertCount(6, $result);
    }

    public function testGetOnlyTrashedReturnsOnlySoftDeletedRecords()
    {
        $result = $this->model->onlyTrashed()->get();

        $this->assertInstanceOf(ModelCollection::class, $result);

        $this->assertCount(2, $result);
    }

    public function testPaginateExcludesDeleted()
    {
        $paginator = $this->model->paginate(3);

        $this->assertInstanceOf(Paginator::class, $paginator);

        foreach ($paginator->data() as $product) {
            $this->assertNull($product->prop('deleted_at'));
        }
    }

    public function testCountExcludesDeleted()
    {
        $this->assertEquals(4, $this->model->count());

        $this->assertEquals(6, ModelFactory::get(TestProductsModel::class)->withTrashed()->count());
    }

    public function testFindOneRespectsSoftDelete()
    {
        $product = $this->model->findOne(3);

        $this->assertTrue($product->isEmpty());

        $product = ModelFactory::get(TestProductsModel::class)->withTrashed()->findOne(3);

        $this->assertFalse($product->isEmpty());

        $this->assertEquals('Product C', $product->prop('title'));
    }

    public function testFindOneByRespectsSoftDelete()
    {
        $product = $this->model->findOneBy('title', 'Product C');

        $this->assertTrue($product->isEmpty());

        $product = ModelFactory::get(TestProductsModel::class)->withTrashed()->findOneBy('title', 'Product C');

        $this->assertFalse($product->isEmpty());
    }

    public function testFirstExcludesDeleted()
    {
        $product = $this->model->criteria('title', '=', 'Product C')->first();

        $this->assertTrue($product->isEmpty());

        $this->assertNull($product->prop('deleted_at'));

        $product = ModelFactory::get(TestProductsModel::class)->withTrashed()->criteria('title', '=', 'Product C')->first();

        $this->assertFalse($product->isEmpty());
    }

    private function _createProductsTableWithData()
    {
        IdiormDbal::execute("CREATE TABLE IF NOT EXISTS products (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title VARCHAR(255),
                    description TEXT,
                    price REAL,
                    created_at DATETIME,
                    deleted_at DATETIME DEFAULT NULL
        )");

        IdiormDbal::execute("INSERT INTO 
                    products 
                        (title, description, price, created_at, deleted_at)
                    VALUES
                        ('Product A', 'High-quality product A', 19.99, '2025-05-01 10:00:00', NULL),
                        ('Product B', 'Eco-friendly product B', 29.99, '2025-05-02 11:00:00', NULL),
                        ('Product C', 'Popular product C', 39.99, '2025-05-03 12:00:00', '2025-05-10 09:00:00'),
                        ('Product D', 'Limited edition product D', 49.99, '2025-05-04 13:00:00', NULL),
                        ('Product E', 'Budget-friendly product E', 59.99, '2025-05-05 14:00:00', '2025-05-11 08:00:00'),
                        ('Product F', 'Premium product F', 59.99, '2025-05-06 15:00:00', NULL)
                    ");
    }
}