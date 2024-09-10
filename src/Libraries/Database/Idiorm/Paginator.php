<?php

namespace Quantum\Libraries\Database\Idiorm;

use Quantum\Libraries\Database\BasePaginator;
use Quantum\Exceptions\DatabaseException;
use IdiormResultSet;

class Paginator extends BasePaginator
{
	/**
	 * @var IdiormDbal
	 */
	private $dbal;

	/**
	 * @var array|IdiormResultSet
	 */
	public $data;

	/**
	 * @param $idiormDbal
	 * @param int $perPage
	 * @param int $page
	 * @throws DatabaseException
	 */
	public function __construct($idiormDbal, int $perPage, int $page = 1)
	{
		/** @var IdiormDbal $idiormDbal */
		$this->total = $idiormDbal->getOrmModel()->count();
		$this->dbal = $idiormDbal;
		$this->dbal->limit($perPage)->offset($perPage * ($page - 1));
		$this->data = $this->dbal->getOrmModel()->find_many();
		$this->perPage = $perPage;
		$this->page = $page;
		$this->baseUrl = base_dir();
	}

	/**
	 * @return mixed
	 */
	public function firstItem()
	{
		if (!is_array($this->data)){
			$this->data = $this->data->as_array();
		}
		return $this->data[array_key_first($this->data)];
	}

	/**
	 * @return mixed
	 */
	public function lastItem()
	{
		if (!is_array($this->data)){
			$this->data = $this->data->as_array();
		}
		return $this->data[array_key_last($this->data)];
	}

	/**
	 * @return array|IdiormResultSet
	 */
	public function data()
	{
		return $this->data ?? [];
	}
}