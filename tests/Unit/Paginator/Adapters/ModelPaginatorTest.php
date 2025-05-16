<?php

namespace Quantum\Tests\Unit\Paginator\Adapters;

use Quantum\Tests\Unit\Paginator\PaginatorTestCase;
use Quantum\Paginator\Adapters\ModelPaginator;
use Quantum\Tests\_root\shared\Models\Post;
use Quantum\Model\Factories\ModelFactory;

class ModelPaginatorTest extends PaginatorTestCase
{
    private $postModel;
    private $paginator;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('base_url', 'http://localhost');

        $this->postModel = ModelFactory::createOrmInstance('posts');

        $this->paginator = new ModelPaginator($this->postModel, Post::class, 2, 1);
    }

    public function testModelPaginatorConstructor()
    {
        $this->assertInstanceOf(ModelPaginator::class, $this->paginator);
    }

    public function testModelPaginatorFromArray()
    {
        $params = [
            'orm' => $this->postModel,
            'model' => Post::class,
            'perPage' => 2,
            'page' => 2,
        ];

        $paginator = ModelPaginator::fromArray($params);

        $this->assertInstanceOf(ModelPaginator::class, $paginator);

        $data = $paginator->data();

        $this->assertIsIterable($data);

        $this->assertEquals(2, $data->count());

        $titles = [];

        foreach ($data as $post) {
            $this->assertInstanceOf(Post::class, $post);

            $titles[] = $post->title;
        }

        $this->assertContains('News', $titles);

        $this->assertContains('Note', $titles);
    }

    public function testModelPaginatorData()
    {
        $data = $this->paginator->data();

        $this->assertIsIterable($data);

        $this->assertEquals(2, $data->count());

        $this->assertInstanceOf(Post::class, $data->first());

        $this->assertEquals('Hi', $data->first()->title);

        $this->assertEquals('First post!', $data->first()->content);
    }

    public function testModelPaginatorFirstItem()
    {
        $firstItem = $this->paginator->firstItem();

        $this->assertInstanceOf(Post::class, $firstItem);

        $this->assertEquals('Hi', $firstItem->title);

        $this->assertEquals('First post!', $firstItem->content);
    }

    public function testModelPaginatorLastItem()
    {
        $lastItem = $this->paginator->lastItem();
        $this->assertInstanceOf(Post::class, $lastItem);
        $this->assertEquals('Hey', $lastItem->title);
        $this->assertEquals('Hello world', $lastItem->content);
    }

    public function testModelPaginatorTotal()
    {
        $this->assertEquals(5, $this->paginator->total());
    }

    public function testModelPaginatorCurrentPageNumber()
    {
        $this->assertEquals(1, $this->paginator->currentPageNumber());
    }

    public function testModelPaginatorPreviousPageNumber()
    {
        $this->assertEquals(1, $this->paginator->previousPageNumber());
    }

    public function testModelPaginatorNextPageNumber()
    {
        $this->assertEquals(2, $this->paginator->nextPageNumber());
    }

    public function testModelPaginatorLastPageNumber()
    {
        $this->assertEquals(3, $this->paginator->lastPageNumber());
    }

    public function testModelPaginatorCurrentPageLink()
    {
        $this->assertEquals('?per_page=2&page=1', $this->paginator->currentPageLink());

        $this->assertEquals('http://localhost?per_page=2&page=1', $this->paginator->currentPageLink(true));
    }

    public function testModelPaginatorFirstPageLink()
    {
        $this->assertEquals('?per_page=2&page=1', $this->paginator->firstPageLink());

        $this->assertEquals('http://localhost?per_page=2&page=1', $this->paginator->firstPageLink(true));
    }

    public function testModelPaginatorPreviousPageLink()
    {
        $this->assertEquals('?per_page=2&page=1', $this->paginator->previousPageLink());

        $this->assertEquals('http://localhost?per_page=2&page=1', $this->paginator->previousPageLink(true));
    }

    public function testModelPaginatorNextPageLink()
    {
        $this->assertEquals('?per_page=2&page=2', $this->paginator->nextPageLink());

        $this->assertEquals('http://localhost?per_page=2&page=2', $this->paginator->nextPageLink(true));
    }

    public function testModelPaginatorLastPageLink()
    {
        $this->assertEquals('?per_page=2&page=3', $this->paginator->lastPageLink());

        $this->assertEquals('http://localhost?per_page=2&page=3', $this->paginator->lastPageLink(true));
    }

    public function testModelPaginatorPerPage()
    {
        $this->assertEquals(2, $this->paginator->perPage());
    }
}