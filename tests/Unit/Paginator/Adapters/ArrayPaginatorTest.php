<?php

namespace Quantum\Tests\Unit\Paginator\Adapters;

use Quantum\Paginator\Adapters\ArrayPaginator;
use Quantum\Tests\Unit\AppTestCase;

class ArrayPaginatorTest extends AppTestCase
{
    private array $items;

    private ArrayPaginator $paginator;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('base_url', 'http://localhost');

        $this->items = [
            ['id' => 1, 'title' => 'Item 1'],
            ['id' => 2, 'title' => 'Item 2'],
            ['id' => 3, 'title' => 'Item 3'],
            ['id' => 4, 'title' => 'Item 4'],
            ['id' => 5, 'title' => 'Item 5'],
        ];

        $this->paginator = new ArrayPaginator($this->items, 2, 1);
    }

    public function testArrayPaginatorConstructor(): void
    {
        $this->assertInstanceOf(ArrayPaginator::class, $this->paginator);
    }

    public function testArrayPaginatorData(): void
    {
        $data = $this->paginator->data();

        $this->assertIsArray($data);

        $this->assertCount(2, $data);

        $this->assertEquals('Item 1', $data[0]['title']);

        $this->assertEquals('Item 2', $data[1]['title']);
    }

    public function testArrayPaginatorFirstItem(): void
    {
        $firstItem = $this->paginator->firstItem();

        $this->assertIsArray($firstItem);

        $this->assertEquals(1, $firstItem['id']);

        $this->assertEquals('Item 1', $firstItem['title']);
    }

    public function testArrayPaginatorLastItem(): void
    {
        $lastItem = $this->paginator->lastItem();

        $this->assertIsArray($lastItem);

        $this->assertEquals(2, $lastItem['id']);

        $this->assertEquals('Item 2', $lastItem['title']);
    }

    public function testArrayPaginatorTotal(): void
    {
        $this->assertEquals(5, $this->paginator->total());
    }

    public function testArrayPaginatorCurrentPageNumber(): void
    {
        $this->assertEquals(1, $this->paginator->currentPageNumber());
    }

    public function testArrayPaginatorPreviousPageNumber(): void
    {
        $this->assertEquals(1, $this->paginator->previousPageNumber());
    }

    public function testArrayPaginatorNextPageNumber(): void
    {
        $this->assertEquals(2, $this->paginator->nextPageNumber());
    }

    public function testArrayPaginatorLastPageNumber(): void
    {
        $this->assertEquals(3, $this->paginator->lastPageNumber());
    }

    public function testArrayPaginatorCurrentPageLink(): void
    {
        $this->assertEquals('?per_page=2&page=1', $this->paginator->currentPageLink());

        $this->assertEquals('http://localhost?per_page=2&page=1', $this->paginator->currentPageLink(true));
    }

    public function testArrayPaginatorFirstPageLink(): void
    {
        $this->assertEquals('?per_page=2&page=1', $this->paginator->firstPageLink());

        $this->assertEquals('http://localhost?per_page=2&page=1', $this->paginator->firstPageLink(true));
    }

    public function testArrayPaginatorPreviousPageLink(): void
    {
        $this->assertEquals('?per_page=2&page=1', $this->paginator->previousPageLink());

        $this->assertEquals('http://localhost?per_page=2&page=1', $this->paginator->previousPageLink(true));
    }

    public function testArrayPaginatorNextPageLink(): void
    {
        $this->assertEquals('?per_page=2&page=2', $this->paginator->nextPageLink());

        $this->assertEquals('http://localhost?per_page=2&page=2', $this->paginator->nextPageLink(true));
    }

    public function testArrayPaginatorLastPageLink(): void
    {
        $this->assertEquals('?per_page=2&page=3', $this->paginator->lastPageLink());

        $this->assertEquals('http://localhost?per_page=2&page=3', $this->paginator->lastPageLink(true));
    }

    public function testArrayPaginatorPerPage(): void
    {
        $this->assertEquals(2, $this->paginator->perPage());
    }

    public function testArrayPaginatorGetPaginationRendersCurrentAndLastPage(): void
    {
        $html = $this->paginator->getPagination();

        $this->assertStringContainsString('>1<', $html);
        $this->assertStringContainsString('>3<', $html);
    }

    public function testArrayPaginatorGetPaginationRendersEllipsisForHiddenPages(): void
    {
        $html = $this->paginator->getPagination();

        $this->assertStringContainsString('<span>...</span>', $html);
    }

    public function testArrayPaginatorGetPaginationRendersMiddlePageWhenCurrentPageIsTwo(): void
    {
        $paginator = new ArrayPaginator($this->items, 2, 2);

        $html = $paginator->getPagination();

        $this->assertStringContainsString('>2<', $html);
    }

}
