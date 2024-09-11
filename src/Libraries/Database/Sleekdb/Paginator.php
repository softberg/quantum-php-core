<?php

namespace Quantum\Libraries\Database\Sleekdb;

use SleekDB\Exceptions\InvalidConfigurationException;
use SleekDB\Exceptions\InvalidArgumentException;
use Quantum\Libraries\Database\BasePaginator;
use Quantum\Exceptions\DatabaseException;
use Quantum\Exceptions\ModelException;
use SleekDB\Exceptions\IOException;

class Paginator extends BasePaginator
{
	/**
	 * @var SleekDbal
	 */
	private $dbal;

	/**
	 * @var array
	 */
	public $data;

	/**
	 * @param $sleekDbal
	 * @param int $perPage
	 * @param int $page
	 * @throws DatabaseException
	 * @throws ModelException
	 * @throws IOException
	 * @throws InvalidArgumentException
	 * @throws InvalidConfigurationException
	 */
	public function __construct($sleekDbal, int $perPage, int $page = 1)
	{
		/** @var SleekDbal $sleekDbal */
		$this->total = count($sleekDbal->getBuilder()->getQuery()->fetch());
		$this->dbal = $sleekDbal;
		$this->dbal->limit($perPage)->offset($perPage * ($page - 1));
		$this->data = $this->dbal->getBuilder()->getQuery()->fetch();
		$this->perPage = $perPage;
		$this->page = $page;
		$this->baseUrl = base_url();
	}

	/**
	 * @return mixed
	 */
	public function firstItem()
	{
		return $this->data[array_key_first($this->data)];
	}

	/**
	 * @return mixed
	 */
	public function lastItem()
	{
		return $this->data[array_key_last($this->data)];
	}

	/**
	 * @return array|SleekDbal[]
	 */
	public function data(): array
	{
		$result = array_map(function ($element) {
			$item = clone $this->dbal;
			$item->setData($element);
			$item->setModifiedFields($element);
			$item->setIsNew(false);
			return $item;
		}, $this->data);

		return $result ?? [];
	}
}