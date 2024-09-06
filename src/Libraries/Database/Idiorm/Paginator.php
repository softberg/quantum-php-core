<?php

namespace Quantum\Libraries\Database\Idiorm;

use Quantum\Libraries\Database\PaginatorInterface;
use Quantum\Exceptions\DatabaseException;
use IdiormResultSet;

class Paginator implements PaginatorInterface
{
	/**
	 * @var int
	 */
	private $total;

	/**
	 * @var IdiormDbal
	 */
	private $dbal;

	/**
	 * @var int
	 */
	protected $per_page;

	/**
	 * @var int
	 */
	protected $page;

	/**
	 * @var array|IdiormResultSet
	 */
	public $data;

	/**
	 * @param $idiormDbal
	 * @param int $per_page
	 * @param int $page
	 * @throws DatabaseException
	 */
	public function __construct($idiormDbal, int $per_page, int $page = 1)
	{
		/** @var IdiormDbal $idiormDbal */
		$this->setTotal($idiormDbal);
		$this->dbal = $idiormDbal;
		$this->dbal->limit($per_page)->offset($per_page * ($page - 1));
		$this->data = $this->dbal->getOrmModel()->find_many();
		$this->per_page = $per_page;
		$this->page = $page;
	}

	/**
	 * @return int
	 */
	public function currentPageNumber(): int
	{
		return $this->page;
	}

	/**
	 * @param bool $withBaseUrl
	 * @return string|null
	 */
	public function currentPageLink(bool $withBaseUrl = false): ?string
	{
		$current = null;
		if (!empty($this->page)) {
			$current = $this->getUri($withBaseUrl) . 'per_page=' . $this->per_page . '&page=' . $this->page;
		}
		return $current;
	}

	/**
	 * @return int|null
	 */
	public function previousPageNumber(): ?int
	{
		$previous = null;
		if ($this->page > 1) {
			$previous = $this->page - 1;
		} elseif ($this->page == 1) {
			$previous = $this->page;
		}

		return $previous;
	}

	/**
	 * @param bool $withBaseUrl
	 * @return string|null
	 */
	public function previousPageLink(bool $withBaseUrl = false): ?string
	{
		$previous = null;
		if (!empty($this->previousPageNumber())) {
			$previous = $this->getUri($withBaseUrl) . 'per_page=' . $this->per_page . '&page=' . $this->previousPageNumber();
		}
		return $previous;
	}

	/**
	 * @return int|null
	 */
	public function nextPageNumber(): ?int
	{
		$next = null;
		if ($this->page < $this->lastPageNumber()) {
			$next = $this->page + 1;
		} elseif ($this->page == $this->lastPageNumber()) {
			$next = $this->page;
		}
		return $next;
	}

	/**
	 * @param bool $withBaseUrl
	 * @return string|null
	 */
	public function nextPageLink(bool $withBaseUrl = false): ?string
	{
		$next = null;
		if (!empty($this->nextPageNumber())) {
			$next = $this->getUri($withBaseUrl) . 'per_page=' . $this->per_page . '&page=' . $this->nextPageNumber();
		}
		return $next;
	}

	/**
	 * @param bool $withBaseUrl
	 * @return string|null
	 */
	public function firstPageLink(bool $withBaseUrl = false): ?string
	{
		return $this->getUri($withBaseUrl) . 'per_page=' . $this->per_page . '&page=1';
	}

	/**
	 * @return int
	 */
	public function lastPageNumber(): int
	{
		return (int)ceil($this->total() / $this->per_page);
	}

	/**
	 * @param bool $withBaseUrl
	 * @return string|null
	 */
	public function lastPageLink(bool $withBaseUrl = false): ?string
	{
		$last = null;
		if (!empty($this->lastPageNumber())) {
			$last = $this->getUri($withBaseUrl) . 'per_page=' . $this->per_page . '&page=' . $this->lastPageNumber();
		}
		return $last;
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
	 * @return int
	 */
	public function perPage()
	{
		return $this->per_page;
	}

	/**
	 * @return int
	 */
	public function total()
	{
		return $this->total;
	}

	/**
	 * @param bool $withBaseUrl
	 * @return array
	 */
	public function links(bool $withBaseUrl = false): array
	{
		$links = [];
		for ($i = 1; $i <= $this->lastPageNumber(); $i++) {
			$links[] = $this->getUri($withBaseUrl) . 'per_page=' . $this->per_page . '&page=' . $i;
		}

		return $links;
	}

	/**
	 * @param bool $withBaseUrl
	 * @param $pageItemsCount
	 * @return string|null
	 */
	public function getPagination(bool $withBaseUrl = false, $pageItemsCount = null): ?string
	{
		if (!is_null($pageItemsCount) && $pageItemsCount < 3) {
			$pageItemsCount = 3;
		}

		$currentPage = $this->currentPageNumber();
		$totalPages = $this->lastPageNumber();

		if ($totalPages <= 1) {
			return null;
		}

		$pagination = '<ul class="pagination">';

		if ($currentPage > 1) {
			$pagination .= '<li><a href="' . $this->previousPageLink() . '">&laquo; Previous</a></li>';
		}

		if ($pageItemsCount) {
			$startPage = max(1, $currentPage - ceil(($pageItemsCount - 3) / 2));
			$endPage = min($totalPages, $startPage + $pageItemsCount - 3);
			$startPage = max(1, $endPage - $pageItemsCount + 3);

			if ($startPage > 1) {
				$pagination .= '<li><a href="' . $this->firstPageLink() . '">1</a></li>';
				if ($startPage > 2) {
					$pagination .= '<li><span>...</span></li>';
				}
			}

			$links = $this->links($withBaseUrl);
			for ($i = $startPage; $i <= $endPage; $i++) {
				$active = $i == $currentPage ? 'class="active"' : '';
				$pagination .= '<li ' . $active . '><a href="' . $links[$i - 1] . '">' . $i . '</a></li>';
			}

			if ($endPage < $totalPages) {
				if ($endPage < $totalPages - 1) {
					$pagination .= '<li><span>...</span></li>';
				}

				$pagination .= '<li><a href="' . $links[$totalPages - 1] . '">' . $totalPages . '</a></li>';
			}
		}

		if ($currentPage < $totalPages) {
			$pagination .= '<li><a href="' . $this->nextPageLink() . '">Next &raquo;</a></li>';
		}

		$pagination .= '</ul>';

		return $pagination;
	}

	/**
	 * @return array|IdiormResultSet
	 */
	public function data()
	{
		return $this->data ?? [];
	}

	/**
	 * @param IdiormDbal $idiormDbal
	 * @return void
	 * @throws DatabaseException
	 */
	private function setTotal(IdiormDbal $idiormDbal)
	{
		$this->total = $idiormDbal->getOrmModel()->count();
	}

	/**
	 * @param bool $withBaseUrl
	 * @return string
	 */
	private function getUri(bool $withBaseUrl = false)
	{
		$base_url = base_url();
		$routeUrl = preg_replace('/([?&](page|per_page)=\d+)/', '', route_uri());
		$routeUrl = preg_replace('/&/', '?', $routeUrl, 1);
		$url = $routeUrl;
		if ($withBaseUrl) {
			$url = $base_url . $routeUrl;
		}

		$delimiter = str_contains($url, '?') ? '&' : '?';

		return $url . $delimiter;
	}
}