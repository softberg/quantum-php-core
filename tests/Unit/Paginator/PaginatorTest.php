<?php

namespace Quantum\Tests\Unit\Paginator;

use Quantum\Paginator\Exceptions\PaginatorException;
use Quantum\Paginator\Contracts\PaginatorInterface;
use Quantum\Paginator\Adapters\ArrayPaginator;
use Quantum\Paginator\Adapters\ModelPaginator;
use Quantum\Tests\_root\shared\Models\Post;
use Quantum\Model\Factories\ModelFactory;
use Quantum\Paginator\Paginator;

class PaginatorTest extends PaginatorTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testPaginatorGetAdapter()
    {
        $paginator = new Paginator(ArrayPaginator::fromArray([
            'items' => [],
            'perPage' => 2,
            'page' => 2,
        ]));

        $this->assertInstanceOf(ArrayPaginator::class, $paginator->getAdapter());

        $this->assertInstanceOf(PaginatorInterface::class, $paginator->getAdapter());

        $paginator = new Paginator(ModelPaginator::fromArray([
            'orm' => ModelFactory::createOrmInstance('posts'),
            'model' => Post::class,
            'perPage' => 2,
            'page' => 2,
        ]));

        $this->assertInstanceOf(ModelPaginator::class, $paginator->getAdapter());

        $this->assertInstanceOf(PaginatorInterface::class, $paginator->getAdapter());
    }

    public function testPaginatorCallingValidMethod()
    {
        $paginator = new Paginator(ArrayPaginator::fromArray([
            'items' => [],
            'perPage' => 2,
            'page' => 2,
        ]));

        $this->assertEquals(0, $paginator->total());
    }

    public function testPaginatorCallingInvalidMethod()
    {
        $paginator = new Paginator(ArrayPaginator::fromArray([
            'items' => [],
            'perPage' => 2,
            'page' => 2,
        ]));

        $this->expectException(PaginatorException::class);

        $this->expectExceptionMessage('The method `callingInvalidMethod` is not supported for `' . ArrayPaginator::class . '`');

        $paginator->callingInvalidMethod();
    }
}

