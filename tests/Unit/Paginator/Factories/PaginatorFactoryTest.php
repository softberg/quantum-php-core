<?php

namespace Quantum\Tests\Unit\Paginator\Factories;

use Quantum\Model\DbModel;
use Quantum\Paginator\Exceptions\PaginatorException;
use Quantum\Tests\_root\shared\Models\TestPostModel;
use Quantum\Paginator\Contracts\PaginatorInterface;
use Quantum\Tests\Unit\Paginator\PaginatorTestCase;
use Quantum\Paginator\Factories\PaginatorFactory;
use Quantum\Paginator\Adapters\ModelPaginator;
use Quantum\Paginator\Adapters\ArrayPaginator;
use Quantum\Model\Factories\ModelFactory;
use Quantum\Paginator\Paginator;

class PaginatorFactoryTest extends PaginatorTestCase
{
    private DbModel $postModel;

    public function setUp(): void
    {
        parent::setUp();

        $this->postModel = ModelFactory::createDynamicModel('posts', TestPostModel::class);
    }

    public function testPaginatorFactoryInstance(): void
    {
        $paginator = PaginatorFactory::create(Paginator::ARRAY, [
            'items' => [],
            'perPage' => 2,
            'page' => 2,
        ]);

        $this->assertInstanceOf(Paginator::class, $paginator);
    }

    public function testPaginatorFactoryArrayAdapter(): void
    {
        $paginator = PaginatorFactory::create(Paginator::ARRAY, [
            'items' => [],
            'perPage' => 2,
            'page' => 2,
        ]);

        $this->assertInstanceOf(ArrayPaginator::class, $paginator->getAdapter());

        $this->assertInstanceOf(PaginatorInterface::class, $paginator->getAdapter());
    }

    public function testPaginatorFactoryModelAdapter(): void
    {
        $paginator = PaginatorFactory::create(Paginator::MODEL, [
            'model' => $this->postModel,
            'perPage' => 2,
            'page' => 2,
        ]);

        $this->assertInstanceOf(ModelPaginator::class, $paginator->getAdapter());

        $this->assertInstanceOf(PaginatorInterface::class, $paginator->getAdapter());
    }

    public function testPaginatorFactoryInvalidAdapter(): void
    {
        $this->expectException(PaginatorException::class);

        $this->expectExceptionMessage('The adapter `invalid_type` is not supported');

        PaginatorFactory::create('invalid_type', [
            'items' => [],
            'perPage' => 2,
            'page' => 2,
        ]);
    }
}
