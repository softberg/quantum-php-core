<?php

namespace Quantum\Tests\Unit\Paginator;

use Quantum\Paginator\Exceptions\PaginatorException;
use Quantum\Tests\_root\shared\Models\TestPostModel;
use Quantum\Paginator\Contracts\PaginatorInterface;
use Quantum\Paginator\Adapters\ArrayPaginator;
use Quantum\Paginator\Adapters\ModelPaginator;
use Quantum\Model\Factories\ModelFactory;
use Quantum\Paginator\Paginator;

class PaginatorTest extends PaginatorTestCase
{
    private $model;

    public function setUp(): void
    {
        parent::setUp();

        $this->model = ModelFactory::createDynamicModel('posts', TestPostModel::class);
    }

    public function testPaginatorGetAdapter()
    {
        $paginator = new Paginator(new ArrayPaginator([], 2, 2));

        $this->assertInstanceOf(ArrayPaginator::class, $paginator->getAdapter());

        $this->assertInstanceOf(PaginatorInterface::class, $paginator->getAdapter());

        $paginator = new Paginator(new ModelPaginator($this->model, 2, 2));

        $this->assertInstanceOf(ModelPaginator::class, $paginator->getAdapter());

        $this->assertInstanceOf(PaginatorInterface::class, $paginator->getAdapter());
    }

    public function testPaginatorCallingValidMethod()
    {
        $paginator = new Paginator(new ArrayPaginator([], 2, 2));

        $this->assertEquals(0, $paginator->total());
    }

    public function testPaginatorCallingInvalidMethod()
    {
        $paginator = new Paginator(new ArrayPaginator([], 2, 2));

        $this->expectException(PaginatorException::class);

        $this->expectExceptionMessage('The method `callingInvalidMethod` is not supported for `' . ArrayPaginator::class . '`');

        $paginator->callingInvalidMethod();
    }
}
