<?php

namespace Quantum\Tests\Unit\Paginator\Factories;

use Quantum\Paginator\Exceptions\PaginatorException;
use Quantum\Paginator\Contracts\PaginatorInterface;
use Quantum\Tests\Unit\Paginator\PaginatorTestCase;
use Quantum\Paginator\Factories\PaginatorFactory;
use Quantum\Paginator\Adapters\ModelPaginator;
use Quantum\Paginator\Adapters\ArrayPaginator;
use Quantum\Tests\_root\shared\Models\Post;
use Quantum\Model\Factories\ModelFactory;
use Quantum\Paginator\Paginator;

class PaginatorFactoryTest extends PaginatorTestCase
{
    private $postModel;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testPaginatorFactoryInstance()
    {
        $paginator = PaginatorFactory::get(Paginator::ARRAY, [
            'items' => [],
            'perPage' => 2,
            'page' => 2,
        ]);

        $this->assertInstanceOf(Paginator::class, $paginator);
    }

    public function testPaginatorFactoryArrayAdapter()
    {
        $paginator = PaginatorFactory::get(Paginator::ARRAY, [
            'items' => [],
            'perPage' => 2,
            'page' => 2,
        ]);

        $this->assertInstanceOf(ArrayPaginator::class, $paginator->getAdapter());

        $this->assertInstanceOf(PaginatorInterface::class, $paginator->getAdapter());
    }

    public function testPaginatorFactoryModelAdapter()
    {
        $paginator = PaginatorFactory::get(Paginator::MODEL, [
            'orm' => ModelFactory::createOrmInstance('posts'),
            'model' => Post::class,
            'perPage' => 2,
            'page' => 2,
        ]);

        $this->assertInstanceOf(ModelPaginator::class, $paginator->getAdapter());

        $this->assertInstanceOf(PaginatorInterface::class, $paginator->getAdapter());
    }

    public function testPaginatorFactoryInvalidAdapter()
    {
        $this->expectException(PaginatorException::class);

        $this->expectExceptionMessage('The adapter `invalid_type` is not supported`');

        PaginatorFactory::get('invalid_type', [
            'items' => [],
            'perPage' => 2,
            'page' => 2,
        ]);
    }

    public function testPaginatorFactoryReturnsSameInstance()
    {
        $app1 = PaginatorFactory::get(Paginator::ARRAY, [
            'items' => [],
            'perPage' => 2,
            'page' => 2,
        ]);

        $app2 = PaginatorFactory::get(Paginator::ARRAY, [
            'items' => [],
            'perPage' => 2,
            'page' => 2,
        ]);

        $this->assertSame($app1, $app2);
    }
}