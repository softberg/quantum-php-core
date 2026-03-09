<?php

namespace Quantum\Tests\Unit\Paginator\Adapters;

use Quantum\Tests\_root\shared\Models\TestPostModel;
use Quantum\Tests\Unit\Paginator\PaginatorTestCase;
use Quantum\Paginator\Adapters\ModelPaginator;
use Quantum\Model\Factories\ModelFactory;
use Quantum\Model\ModelCollection;
use Quantum\Model\DbModel;

class ModelPaginatorTest extends PaginatorTestCase
{
    private ModelPaginator $paginator;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('base_url', 'http://localhost');

        $postModel = ModelFactory::createDynamicModel('posts', TestPostModel::class);

        $this->paginator = new ModelPaginator($postModel, 2, 1);
    }

    public function testModelPaginatorConstructor(): void
    {
        $this->assertInstanceOf(ModelPaginator::class, $this->paginator);
    }

    public function testModelPaginatorData(): void
    {
        $data = $this->paginator->data();

        $this->assertIsIterable($data);

        $this->assertInstanceOf(ModelCollection::class, $data);

        $this->assertInstanceOf(ModelCollection::class, $data);

        $this->assertEquals(2, $data->count());

        $record = $data->first();

        $this->assertInstanceOf(DbModel::class, $record);

        $this->assertInstanceOf(TestPostModel::class, $record);

        $this->assertEquals('Hi', $record->title);

        $this->assertEquals('First post!', $record->content);
    }

    public function testModelPaginatorDataWithAnonymousModel(): void
    {
        $dynamicModel = ModelFactory::createDynamicModel('posts');

        $this->paginator = new ModelPaginator($dynamicModel, 2, 1);

        $data = $this->paginator->data();

        $this->assertIsIterable($data);

        $this->assertInstanceOf(ModelCollection::class, $data);

        $this->assertEquals(2, $data->count());

        $record = $data->first();

        $this->assertInstanceOf(DbModel::class, $record);

        $this->assertStringContainsString('@anonymous', get_class($record));

        $this->assertEquals('Hi', $record->title);

        $this->assertEquals('First post!', $record->content);
    }

    public function testModelPaginatorFirstItem(): void
    {
        $firstItem = $this->paginator->firstItem();

        $this->assertInstanceOf(TestPostModel::class, $firstItem);

        $this->assertEquals('Hi', $firstItem->title);

        $this->assertEquals('First post!', $firstItem->content);
    }

    public function testModelPaginatorLastItem(): void
    {
        $lastItem = $this->paginator->lastItem();
        $this->assertInstanceOf(TestPostModel::class, $lastItem);
        $this->assertEquals('Hey', $lastItem->title);
        $this->assertEquals('Hello world', $lastItem->content);
    }

    public function testModelPaginatorTotal(): void
    {
        $this->assertEquals(5, $this->paginator->total());
    }

    public function testModelPaginatorCurrentPageNumber(): void
    {
        $this->assertEquals(1, $this->paginator->currentPageNumber());
    }

    public function testModelPaginatorPreviousPageNumber(): void
    {
        $this->assertEquals(1, $this->paginator->previousPageNumber());
    }

    public function testModelPaginatorNextPageNumber(): void
    {
        $this->assertEquals(2, $this->paginator->nextPageNumber());
    }

    public function testModelPaginatorLastPageNumber(): void
    {
        $this->assertEquals(3, $this->paginator->lastPageNumber());
    }

    public function testModelPaginatorCurrentPageLink(): void
    {
        $this->assertEquals('?per_page=2&page=1', $this->paginator->currentPageLink());

        $this->assertEquals('http://localhost?per_page=2&page=1', $this->paginator->currentPageLink(true));
    }

    public function testModelPaginatorFirstPageLink(): void
    {
        $this->assertEquals('?per_page=2&page=1', $this->paginator->firstPageLink());

        $this->assertEquals('http://localhost?per_page=2&page=1', $this->paginator->firstPageLink(true));
    }

    public function testModelPaginatorPreviousPageLink(): void
    {
        $this->assertEquals('?per_page=2&page=1', $this->paginator->previousPageLink());

        $this->assertEquals('http://localhost?per_page=2&page=1', $this->paginator->previousPageLink(true));
    }

    public function testModelPaginatorNextPageLink(): void
    {
        $this->assertEquals('?per_page=2&page=2', $this->paginator->nextPageLink());

        $this->assertEquals('http://localhost?per_page=2&page=2', $this->paginator->nextPageLink(true));
    }

    public function testModelPaginatorLastPageLink(): void
    {
        $this->assertEquals('?per_page=2&page=3', $this->paginator->lastPageLink());

        $this->assertEquals('http://localhost?per_page=2&page=3', $this->paginator->lastPageLink(true));
    }

    public function testModelPaginatorPerPage(): void
    {
        $this->assertEquals(2, $this->paginator->perPage());
    }

    public function testModelPaginatorGetPaginationRendersCurrentAndLastPage(): void
    {
        $html = $this->paginator->getPagination();

        $this->assertStringContainsString('>1<', $html);
        $this->assertStringContainsString('>3<', $html);
    }

    public function testModelPaginatorGetPaginationRendersEllipsisForHiddenPages(): void
    {
        $html = $this->paginator->getPagination();

        $this->assertStringContainsString('<span>...</span>', $html);
    }

    public function testModelPaginatorGetPaginationRendersMiddlePageWhenCurrentPageIsTwo(): void
    {
        $postModel = ModelFactory::createDynamicModel('posts', TestPostModel::class);

        $paginator = new ModelPaginator($postModel, 2, 2);

        $html = $paginator->getPagination();

        $this->assertStringContainsString('>2<', $html);
    }

}
