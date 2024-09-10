<?php

namespace Quantum\Libraries\Database;

abstract class BasePaginator implements PaginatorInterface
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
	protected const MINIMUM_PAGE_ITEMS_COUNT = 3;

	/**
	 * @var int
	 */
	protected const EDGE_PADDING = 3;

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
		$totalPages = $this->lastPageNumber();
		if ($totalPages <= 1) {
			return null;
		}

		if (!is_null($pageItemsCount) && $pageItemsCount < self::MINIMUM_PAGE_ITEMS_COUNT) {
			$pageItemsCount = self::MINIMUM_PAGE_ITEMS_COUNT;
		}

		$pagination = '<ul class="'. self::PAGINATION_CLASS .'">';
		$currentPage = $this->currentPageNumber();
		
		if ($currentPage > 1) {
			$pagination .= $this->getPreviousPageItem($this->previousPageLink());
		}

		if ($pageItemsCount) {
			$links = $this->links($withBaseUrl);
			$startPage = $this->calculateStartPage($currentPage, $pageItemsCount);
			$endPage = $this->calculateEndPage($startPage, $totalPages, $pageItemsCount);
			$pagination = $this->addFirstPageLink($pagination, $startPage);
			$pagination = $this->getItemsLinks($pagination, $startPage, $endPage, $currentPage, $links);
			$pagination = $this->addLastPageLink($pagination, $endPage, $totalPages, $links);
		}

		if ($currentPage < $totalPages) {
			$pagination .= $this->getNextPageItem($this->nextPageLink());
		}

		$pagination .= '</ul>';

		return $pagination;
	}

	/**
	 * @param string|null $nextPageLink
	 * @return string
	 */
	protected function getNextPageItem(?string $nextPageLink): string
	{
		$link = '';
		if (!empty($nextPageLink)){
			$link = '<li><a href="' . $nextPageLink . '">Next &raquo;</a></li>';
		}
		return $link;
	}

	/**
	 * @param string|null $previousPageLink
	 * @return string
	 */
	protected function getPreviousPageItem(?string $previousPageLink): string
	{
		$link = '';
		if (!empty($previousPageLink)){
			$link = '<li><a href="' . $previousPageLink . '">&laquo; Previous</a></li>';
		}
		return $link;
	}

	protected function getItemsLinks($pagination, $startPage, $endPage, $currentPage, array $links)
	{
		for ($i = $startPage; $i <= $endPage; $i++) {
			$active = $i == $currentPage ? 'class="'. self::PAGINATION_CLASS_ACTIVE .'"' : '';
			$pagination .= '<li ' . $active . '><a href="' . $links[$i - 1] . '">' . $i . '</a></li>';
		}
		return $pagination;
	}

	protected function calculateStartPage($currentPage, $pageItemsCount)
	{
		return max(1, $currentPage - ceil(($pageItemsCount - self::EDGE_PADDING) / 2));
	}

	protected function calculateEndPage($startPage, $totalPages, $pageItemsCount)
	{
		$endPage = min($totalPages, $startPage + $pageItemsCount - self::EDGE_PADDING);
		return max(1, $endPage - $pageItemsCount + self::EDGE_PADDING);
	}

	protected function addFirstPageLink($pagination, $startPage)
	{
		if ($startPage > 1) {
			$pagination .= '<li><a href="' . $this->firstPageLink() . '">'. self::FIRST_PAGE_NUMBER .'</a></li>';
			if ($startPage > 2) {
				$pagination .= '<li><span>...</span></li>';
			}
		}
		return $pagination;
	}

	protected function addLastPageLink($pagination, $endPage, $totalPages, $links)
	{
		if ($endPage < $totalPages) {
			if ($endPage < $totalPages - 1) {
				$pagination .= '<li><span>...</span></li>';
			}
			$pagination .= '<li><a href="' . $links[$totalPages - 1] . '">' . $totalPages . '</a></li>';
		}
		return $pagination;
	}
}