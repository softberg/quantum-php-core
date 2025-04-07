<?php

namespace Quantum\Tests\Unit\Paginator;

use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;
use Quantum\Paginator\Contracts\PaginatorInterface;
use Quantum\Tests\_root\shared\Models\Post;
use Quantum\Model\Factories\ModelFactory;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Paginator\Paginator;


class PaginatorTest extends AppTestCase
{

    private $postModel;

    public function setUp(): void
    {
        parent::setUp();

        IdiormDbal::connect(['driver' => 'sqlite', 'database' => ':memory:']);

        $this->_createPostTableWithData();

        $this->postModel = ModelFactory::createOrmInstance('posts');
    }

    public function tearDown(): void
    {
        IdiormDbal::execute("DROP TABLE IF EXISTS posts");
    }

    public function testConstructor()
    {
        $paginator = new Paginator($this->postModel, Post::class, 1, 1);

        $this->assertInstanceOf(PaginatorInterface::class, $paginator);
    }

    public function testPaginatorData()
    {
        $paginator = new Paginator($this->postModel, Post::class, 2, 2);

        $this->assertIsIterable($paginator->data());

        $this->assertEquals(2, $paginator->data()->count());
    }

    public function testPaginatorCurrentPageNumber()
    {
        $paginator = new Paginator($this->postModel, Post::class, 1, 1);

        $this->assertIsNumeric($paginator->currentPageNumber());

        $this->assertEquals(1, $paginator->currentPageNumber());
    }

    public function testPaginatorCurrentPageLink()
    {
        $paginator = new Paginator($this->postModel, Post::class, 1, 1);

        $this->assertIsString($paginator->currentPageLink());

        $this->assertEquals('?per_page=1&page=1', $paginator->currentPageLink());
    }

    public function testPaginatorPreviousPageNumber()
    {
        $paginator = new Paginator($this->postModel, Post::class, 2, 3);

        $this->assertIsNumeric($paginator->previousPageNumber());

        $this->assertEquals(2, $paginator->previousPageNumber());
    }

    public function testPaginatorPreviousPageLink()
    {
        $paginator = new Paginator($this->postModel, Post::class, 2, 3);

        $this->assertIsString($paginator->previousPageLink());

        $this->assertEquals('?per_page=2&page=2', $paginator->previousPageLink());
    }

    public function testPaginatorNextPageNumber()
    {
        $paginator = new Paginator($this->postModel, Post::class, 2, 2);

        $this->assertIsNumeric($paginator->nextPageNumber());

        $this->assertEquals(3, $paginator->nextPageNumber());
    }

    public function testPaginatorNextPageLink()
    {
        $paginator = new Paginator($this->postModel, Post::class, 2, 2);

        $this->assertIsString($paginator->nextPageLink());

        $this->assertEquals('?per_page=2&page=3', $paginator->nextPageLink());
    }

    public function testPaginatorLastPageNumber()
    {
        $paginator = new Paginator($this->postModel, Post::class, 2, 2);

        $this->assertIsNumeric($paginator->lastPageNumber());

        $this->assertEquals(3, $paginator->lastPageNumber());
    }

    public function testPaginatorLastPageLink()
    {
        $paginator = new Paginator($this->postModel, Post::class, 2, 2);

        $this->assertIsString($paginator->lastPageLink());

        $this->assertEquals(3, $paginator->lastPageNumber());
    }

    public function testPaginatorFirstPageLink()
    {
        $paginator = new Paginator($this->postModel, Post::class, 2, 2);

        $this->assertIsString($paginator->firstPageLink());

        $this->assertEquals('?per_page=2&page=1', $paginator->firstPageLink());
    }

    public function testPaginatorFirstItem()
    {
        $paginator = new Paginator($this->postModel->orderBy('id', 'asc'), Post::class, 2, 2);

        $this->assertInstanceOf(Post::class, $paginator->firstItem());

        $this->assertEquals('News', $paginator->firstItem()->title);

        $this->assertEquals('Big update', $paginator->firstItem()->content);
    }

    public function testPaginatorLastItem()
    {
        $paginator = new Paginator($this->postModel, Post::class, 2, 2);

        $this->assertInstanceOf(Post::class, $paginator->lastItem());

        $this->assertEquals('Note', $paginator->lastItem()->title);

        $this->assertEquals('Quick tip', $paginator->lastItem()->content);
    }

    public function testPaginatorPerPage()
    {
        $paginator = new Paginator($this->postModel, Post::class, 2, 2);

        $this->assertIsNumeric($paginator->perPage());

        $this->assertEquals(2, $paginator->perPage());
    }

    public function testPaginatorTotal()
    {
        $paginator = new Paginator($this->postModel, Post::class, 2, 2);

        $this->assertIsNumeric($paginator->total());

        $this->assertEquals(5, $paginator->total());
    }

    public function testPaginatorLinks()
    {
        $paginator = new Paginator($this->postModel, Post::class, 2, 1);

        $this->assertIsArray($paginator->links());

        $this->assertCount(3, $paginator->links());

        $this->assertEquals('?per_page=2&page=1', $paginator->links()[0]);

        $this->assertEquals('?per_page=2&page=2', $paginator->links()[1]);

        $this->assertEquals('?per_page=2&page=3', $paginator->links()[2]);
    }

    public function testPaginatorGetPagination()
    {
        $paginator = new Paginator($this->postModel, Post::class, 2, 2);

        $this->assertIsString($paginator->getPagination());

        $this->assertEquals(
            '<ul class="pagination"><li><a href="?per_page=2&page=1">common.pagination.prev</a></li><li><a href="?per_page=2&page=3">common.pagination.next</a></li></ul>',
            $paginator->getPagination()
        );
    }

    private function _createPostTableWithData()
    {
        IdiormDbal::execute("CREATE TABLE IF NOT EXISTS posts (
                        id INTEGER PRIMARY KEY,
                        title VARCHAR(255),
                        content VARCHAR(255),
                        author VARCHAR(255),
                        published_at DATETIME,
                        created_at DATETIME
                    )");

        IdiormDbal::execute("INSERT INTO
            posts
                (title, content, author, published_at, created_at)
            VALUES
                ('Hi', 'First post!', 'John Doe', '2020-01-05 12:00:00', '2020-01-04 20:28:33'),
                ('Hey', 'Hello world', 'Jane Du', '2020-03-15 14:30:00', '2020-03-14 10:15:12'),
                ('News', 'Big update', 'Benjamin Gentry', '2020-04-15 09:45:00', '2020-04-14 10:15:12'),
                ('Note', 'Quick tip', 'Rosa Briggs', '2020-05-15 11:20:00', '2020-05-14 10:15:12'),
                ('FYI', 'Just info', 'Nola Ho', '2020-06-15 16:10:00', '2020-06-14 10:15:12')
        ");
    }
}

