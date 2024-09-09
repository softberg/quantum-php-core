<?php

namespace Quantum\Tests\Libraries\Database\Sleekdb;

use Quantum\Libraries\Database\PaginatorInterface;
use Quantum\Libraries\Database\Sleekdb\SleekDbal;
use Quantum\Libraries\Database\Sleekdb\Paginator;
use Quantum\Loader\Setup;
use Quantum\Tests\AppTestCase;


class SleekPaginatorTest extends AppTestCase
{
	/**
	 * @var SleekDbal $userModel
	 */
	private $userModel;

	public function setUp(): void
	{
		parent::setUp();

		config()->import(new Setup('config', 'database'));

		config()->set('database.current', 'sleekdb');

		SleekDbal::connect(config()->get('database.sleekdb'));

		$this->_createUserTableWithData();

		$this->userModel = new SleekDbal('users');
	}

	public function testSleekConstructor()
	{
		$paginator = new Paginator($this->userModel, 2, 1);
		$this->assertInstanceOf(PaginatorInterface::class, $paginator);
	}

	public function testSleekPaginatorCurrentPageNumber()
	{
		$paginator = new Paginator($this->userModel, 2, 1);

		$this->assertIsNumeric($paginator->currentPageNumber());
		$this->assertEquals(1, $paginator->currentPageNumber());
	}

	public function testSleekPaginatorCurrentPageLink()
	{
		$paginator = new Paginator($this->userModel, 2, 1);

		$this->assertIsString($paginator->currentPageLink());
	}

	public function testSleekPaginatorPreviousPageNumber()
	{
		$paginator = new Paginator($this->userModel, 2, 3);

		$this->assertIsNumeric($paginator->previousPageNumber());
		$this->assertEquals(2, $paginator->previousPageNumber());
	}

	public function testSleekPaginatorPreviousPageLink()
	{
		$paginator = new Paginator($this->userModel, 2, 3);

		$this->assertIsString($paginator->previousPageLink());
	}

	public function testSleekPaginatorNextPageNumber()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsNumeric($paginator->nextPageNumber());
		$this->assertEquals(3, $paginator->nextPageNumber());
	}

	public function testSleekPaginatorNextPageLink()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsString($paginator->nextPageLink());
	}

	public function testSleekPaginatorLastPageNumber()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsNumeric($paginator->lastPageNumber());
		$this->assertEquals(3, $paginator->lastPageNumber());
	}

	public function testSleekPaginatorLastPageLink()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsString($paginator->lastPageLink());
	}

	public function testSleekPaginatorFirstPageLink()
	{
		$paginator = new Paginator($this->userModel, 2, 2);
		$this->assertIsString($paginator->firstPageLink());
	}

	public function testSleekPaginatorFirstItem()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsArray($paginator->firstItem());
		$this->assertIsString($paginator->firstItem()['firstname']);
		$this->assertEquals('Benjamin', $paginator->firstItem()['firstname']);
		$this->assertIsString($paginator->firstItem()['lastname']);
		$this->assertEquals('Gentry', $paginator->firstItem()['lastname']);
	}

	public function testSleekPaginatorLastItem()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsArray($paginator->lastItem());
		$this->assertIsString($paginator->lastItem()['firstname']);
		$this->assertEquals('Rosa', $paginator->lastItem()['firstname']);
		$this->assertIsString($paginator->lastItem()['lastname']);
		$this->assertEquals('Briggs', $paginator->lastItem()['lastname']);
	}

	public function testSleekPaginatorPerPage()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsNumeric($paginator->perPage());
		$this->assertEquals(2, $paginator->perPage());
	}

	public function testSleekPaginatorTotal()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsNumeric($paginator->total());
		$this->assertEquals(5, $paginator->total());
	}

	public function testSleekPaginatorLinks()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsArray($paginator->links());
		$this->assertCount(3, $paginator->links());
		$this->assertIsString($paginator->links()[0]);
		$this->assertIsString($paginator->links()[1]);
		$this->assertIsString($paginator->links()[2]);
	}

	public function testSleekPaginatorGetPagination()
	{
		$paginator = new Paginator($this->userModel, 2, 3);

		$this->assertIsString($paginator->getPagination());
	}

	public function testSleekPaginatorData()
	{
		$paginator = new Paginator($this->userModel, 2, 2);

		$this->assertIsArray($paginator->data());
		$this->assertCount(2, $paginator->data());
	}

	public function tearDown(): void
	{
		config()->flush();

		$this->userModel->deleteTable();

		SleekDbal::disconnect();
	}

	private function _createUserTableWithData()
	{
		$this->userModel = new SleekDbal('users');

		$this->userModel->create();
		$this->userModel->prop('firstname', 'John');
		$this->userModel->prop('lastname', 'Doe');
		$this->userModel->prop('age', 45);
		$this->userModel->prop('country', 'Ireland');
		$this->userModel->prop('created_at', date('Y-m-d H:i:s'));
		$this->userModel->save();

		$this->userModel->create();
		$this->userModel->prop('firstname', 'Jane');
		$this->userModel->prop('lastname', 'Du');
		$this->userModel->prop('age', 35);
		$this->userModel->prop('country', 'England');
		$this->userModel->prop('created_at', date('Y-m-d H:i:s'));
		$this->userModel->save();

		$this->userModel->create();
		$this->userModel->prop('firstname', 'Benjamin');
		$this->userModel->prop('lastname', 'Gentry');
		$this->userModel->prop('age', 25);
		$this->userModel->prop('country', 'Glitterlund');
		$this->userModel->prop('created_at', date('Y-m-d H:i:s'));
		$this->userModel->save();

		$this->userModel->create();
		$this->userModel->prop('firstname', 'Rosa');
		$this->userModel->prop('lastname', 'Briggs');
		$this->userModel->prop('age', 55);
		$this->userModel->prop('country', 'Prestralica');
		$this->userModel->prop('created_at', date('Y-m-d H:i:s'));
		$this->userModel->save();

		$this->userModel->create();
		$this->userModel->prop('firstname', 'Nola');
		$this->userModel->prop('lastname', 'Ho');
		$this->userModel->prop('age', 60);
		$this->userModel->prop('country', 'Lynthia');
		$this->userModel->prop('created_at', date('Y-m-d H:i:s'));
		$this->userModel->save();
	}
}

