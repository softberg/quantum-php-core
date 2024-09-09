<?php

namespace Quantum\Tests\Libraries\Database\Idiorm;

use Quantum\Libraries\Database\PaginatorInterface;
use Quantum\Libraries\Database\Idiorm\IdiormDbal;
use Quantum\Libraries\Database\Idiorm\Paginator;
use Quantum\Tests\AppTestCase;


class IdiormPaginatorTest extends AppTestCase
{
	/**
	 * @var IdiormDbal $userModel
	 */
	private $userModel;

	public function setUp(): void
	{
		parent::setUp();

		config()->set('debug', true);

		IdiormDbal::connect(['driver' => 'sqlite', 'database' => ':memory:']);

		$this->_createUserTableWithData();

		$this->userModel = new IdiormDbal('users');
	}

	public function testIdiormConstructor()
	{
		$paginator = new Paginator($this->userModel, 2, 1);
		$this->assertInstanceOf(PaginatorInterface::class, $paginator);
	}

	public function testIdiormPaginatorCurrentPageNumber()
	{
		$paginator = new Paginator($this->userModel, 2, 1);

		$this->assertIsNumeric($paginator->currentPageNumber());
		$this->assertEquals(1, $paginator->currentPageNumber());
	}

	public function testIdiormPaginatorCurrentPageLink()
	{
		$paginator = new Paginator($this->userModel, 2, 1);

		$this->assertIsString($paginator->currentPageLink());
		$this->assertEquals('?per_page=2&page=1', $paginator->currentPageLink());
	}

	public function testIdiormPaginatorPreviousPageNumber()
	{
		$paginator = new Paginator($this->userModel, 2, 3);

		$this->assertIsNumeric($paginator->previousPageNumber());
		$this->assertEquals(2, $paginator->previousPageNumber());
	}

	public function testIdiormPaginatorPreviousPageLink()
	{
		$paginator = new Paginator($this->userModel, 2, 3);

		$this->assertIsString($paginator->previousPageLink());
		$this->assertEquals('?per_page=2&page=2', $paginator->previousPageLink());
	}

	public function testIdiormPaginatorNextPageNumber()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsNumeric($paginator->nextPageNumber());
		$this->assertEquals(3, $paginator->nextPageNumber());
	}

	public function testIdiormPaginatorNextPageLink()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsString($paginator->nextPageLink());
		$this->assertEquals('?per_page=2&page=3', $paginator->nextPageLink());
	}

	public function testIdiormPaginatorLastPageNumber()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsNumeric($paginator->lastPageNumber());
		$this->assertEquals(3, $paginator->lastPageNumber());
	}

	public function testIdiormPaginatorLastPageLink()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsString($paginator->lastPageLink());
		$this->assertEquals('?per_page=2&page=3', $paginator->lastPageLink());
	}

	public function testIdiormPaginatorFirstPageLink()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsString($paginator->firstPageLink());
		$this->assertEquals('?per_page=2&page=1', $paginator->firstPageLink());
	}

	public function testIdiormPaginatorFirstItem()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertInstanceOf(\ORM::class, $paginator->firstItem());
		$this->assertIsString($paginator->firstItem()->firstname);
		$this->assertEquals('Benjamin', $paginator->firstItem()->firstname);
		$this->assertIsString($paginator->firstItem()->lastname);
		$this->assertEquals('Gentry', $paginator->firstItem()->lastname);
	}

	public function testIdiormPaginatorLastItem()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertInstanceOf(\ORM::class, $paginator->lastItem());
		$this->assertIsString($paginator->lastItem()->firstname);
		$this->assertEquals('Rosa', $paginator->lastItem()->firstname);
		$this->assertIsString($paginator->lastItem()->lastname);
		$this->assertEquals('Briggs', $paginator->lastItem()->lastname);
	}

	public function testIdiormPaginatorPerPage()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsNumeric($paginator->perPage());
		$this->assertEquals(2, $paginator->perPage());
	}

	public function testIdiormPaginatorTotal()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsNumeric($paginator->total());
		$this->assertEquals(5, $paginator->total());
	}

	public function testIdiormPaginatorLinks()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsArray($paginator->links());
		$this->assertCount(3, $paginator->links());
		$this->assertIsString($paginator->links()[0]);
		$this->assertEquals('?per_page=2&page=1', $paginator->links()[0]);
		$this->assertIsString($paginator->links()[1]);
		$this->assertEquals('?per_page=2&page=2', $paginator->links()[1]);
		$this->assertIsString($paginator->links()[2]);
		$this->assertEquals('?per_page=2&page=3', $paginator->links()[2]);
	}

	public function testIdiormPaginatorGetPagination()
	{
		$paginator = new Paginator($this->userModel, 2, 3);

		$this->assertIsString($paginator->getPagination());
		$this->assertEquals('<ul class="pagination"><li><a href="?per_page=2&page=2">&laquo; Previous</a></li></ul>', $paginator->getPagination());
	}

	public function testIdiormPaginatorData()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsArray($paginator->data());
		$this->assertCount(2, $paginator->data());
	}

	public function tearDown(): void
	{
		IdiormDbal::disconnect();
	}

	private function _createUserTableWithData()
	{
		IdiormDbal::execute("CREATE TABLE users (
                        id INTEGER PRIMARY KEY,
                        firstname VARCHAR(255),
                        lastname VARCHAR(255),
                        age INTEGER(11),
                        country VARCHAR(255),
                        created_at DATETIME
                    )");

		IdiormDbal::execute("INSERT INTO 
                    users
                        (firstname, lastname, age, country, created_at) 
                    VALUES
                        ('John', 'Doe', 45, 'Ireland', '2020-01-04 20:28:33'), 
                        ('Jane', 'Du', 35, 'England', '2020-03-14 10:15:12'),
                        ('Benjamin', 'Gentry', 25, 'Glitterlund', '2020-04-14 10:15:12'),
                        ('Rosa', 'Briggs', 55, 'Prestralica', '2020-05-14 10:15:12'),
                        ('Nola ', 'Ho', 60, 'Lynthia', '2020-06-14 10:15:12')
                    ");
	}
}

