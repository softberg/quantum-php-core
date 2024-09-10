<?php

namespace Quantum\Libraries\Database;

class BasePaginator implements PaginatorInterface
{
	/**
	 * @var string
	 */
	protected const PAGINATION_CLASS = 'pagination';

	/**
	 * @var string
	 */
	protected const PAGINATION_CLASS_ACTIVE = 'active';

	/**
	 * @var string
	 */
	protected const PER_PAGE = 'per_page';

	/**
	 * @var string
	 */
	protected const PAGE = 'page';

	/**
	 * @var int
	 */
	protected const FIRST_PAGE_NUMBER = 1;

	/**
	 * @var int
	 */
	protected $total;

	/**
	 * @var string
	 */
	protected $baseUrl;

	/**
	 * @var int
	 */
	protected $perPage;

	/**
	 * @var int
	 */
	protected $page;

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
		return $this->getPageLink($this->page, $withBaseUrl);
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
		return $this->getPageLink($this->previousPageNumber(), $withBaseUrl);
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
		return $this->getPageLink($this->nextPageNumber(), $withBaseUrl);
	}

	/**
	 * @param bool $withBaseUrl
	 * @return string|null
	 */
	public function firstPageLink(bool $withBaseUrl = false): ?string
	{
		return $this->getPageLink(self::FIRST_PAGE_NUMBER, $withBaseUrl);
	}

	/**
	 * @return int
	 */
	public function lastPageNumber(): int
	{
		return (int)ceil($this->total() / $this->perPage);
	}

	/**
	 * @param bool $withBaseUrl
	 * @return string|null
	 */
	public function lastPageLink(bool $withBaseUrl = false): ?string
	{
		return $this->getPageLink($this->lastPageNumber(), $withBaseUrl);
	}

	/**
	 * @return int
	 */
	public function perPage(): int
	{
		return $this->perPage;
	}

	/**
	 * @return int
	 */
	public function total(): int
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
			$links[] = $this->getPageLink($i, $withBaseUrl);
		}

		return $links;
	}

	/**
	 * @param bool $withBaseUrl
	 * @return string
	 */
	protected function getUri(bool $withBaseUrl = false): string
	{
		$routeUrl = preg_replace('/([?&](page|per_page)=\d+)/', '', route_uri());
		$routeUrl = preg_replace('/&/', '?', $routeUrl, 1);
		$url = $routeUrl;

		if ($withBaseUrl) {
			$url = $this->baseUrl . $routeUrl;
		}

		$delimiter = strpos($url, '?') ? '&' : '?';
		return $url . $delimiter;
	}

	protected function getPageLink($pageNumber, $withBaseUrl = false): ?string
	{
		if (!empty($pageNumber)){
			return $this->getUri($withBaseUrl) . self::PER_PAGE .'=' . $this->perPage . '&'. self::PAGE .'=' . $pageNumber;
		}
		return null;
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

		$pagination = '<ul class="'. self::PAGINATION_CLASS .'">';

		if ($currentPage > 1) {
			$pagination .= $this->getPreviousPageItem($this->previousPageLink());
		}

		if ($pageItemsCount) {
			$pagination = $this->getPageItems($pagination, $currentPage, $totalPages, $pageItemsCount, $withBaseUrl);
		}

		if ($currentPage < $totalPages) {
			$pagination .= $this->getNextPageItem($this->nextPageLink());
		}

		$pagination .= '</ul>';

		return $pagination;
	}

	/**
	 * @param $pagination
	 * @param $currentPage
	 * @param $totalPages
	 * @param $pageItemsCount
	 * @param $withBaseUrl
	 * @return mixed|string
	 */
	protected function getPageItems($pagination, $currentPage, $totalPages, $pageItemsCount, $withBaseUrl = false)
	{
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
			$active = $i == $currentPage ? 'class="'. self::PAGINATION_CLASS_ACTIVE .'"' : '';
			$pagination .= '<li ' . $active . '><a href="' . $links[$i - 1] . '">' . $i . '</a></li>';
		}

		if ($endPage < $totalPages) {
			if ($endPage < $totalPages - 1) {
				$pagination .= '<li><span>...</span></li>';
			}

			$pagination .= '<li><a href="' . $links[$totalPages - 1] . '">' . $totalPages . '</a></li>';
		}
		return $pagination;
	}

	/**
	 * @param string $nextPageLink
	 * @return string
	 */
	protected function getNextPageItem(string $nextPageLink): string
	{
		return '<li><a href="' . $nextPageLink . '">Next &raquo;</a></li>';
	}

	/**
	 * @param string $previousPageLink
	 * @return string
	 */
	protected function getPreviousPageItem(string $previousPageLink): string
	{
		return '<li><a href="' . $previousPageLink . '">&laquo; Previous</a></li>';
	}

	public function firstItem()
	{
		// TODO: Implement firstItem() method.
	}

	public function lastItem()
	{
		// TODO: Implement lastItem() method.
	}

	public function data()
	{
		// TODO: Implement data() method.
	}
}