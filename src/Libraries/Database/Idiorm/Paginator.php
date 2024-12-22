<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.9.5
 */

namespace Quantum\Libraries\Database\Idiorm;

use Quantum\Libraries\Database\DatabaseException;
use Quantum\Libraries\Database\BasePaginator;
use IdiormResultSet;

/**
 * Class Paginator
 * @package Quantum\Libraries\Database
 */
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
		$this->total = $idiormDbal->getOrmModel()->count();
		$this->dbal = $idiormDbal;
		$this->dbal->limit($perPage)->offset($perPage * ($page - 1));
		$this->data = $this->dbal->getOrmModel()->find_many();
		$this->perPage = $perPage;
		$this->page = $page;
		$this->baseUrl = base_dir();
	}

	/**
	 * @inheritDoc
	 */
	public function firstItem()
	{
		if (!is_array($this->data)){
			$this->data = $this->data->as_array();
		}

		return $this->data[array_key_first($this->data)];
	}

	/**
	 * @inheritDoc
	 */
	public function lastItem()
	{
		if (!is_array($this->data)){
			$this->data = $this->data->as_array();
		}

		return $this->data[array_key_last($this->data)];
	}

	/**
	 * @inheritDoc
	 */
	public function data(): array
    {
		if (!empty($this->data) && !is_array($this->data)){
			$this->data = $this->data->as_array();
		}

		return $this->data ?? [];
	}
}