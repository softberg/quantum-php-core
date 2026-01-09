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
 * @since 3.0.0
 */

namespace Quantum\Paginator\Traits;

use Quantum\Paginator\Enums\Pagination;

/**
 * Trait PaginatorTrait
 * @package Quantum\Paginator
 */
trait PaginatorTrait
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var int
     */
    protected $total;

    /**
     * @var int
     */
    protected $perPage;

    /**
     * @var int
     */
    protected $page;

    /**
     * @param int $perPage
     * @param int $page
     */
    protected function initialize(int $perPage, int $page = 1): void
    {
        $this->baseUrl = base_url();
        $this->perPage = $perPage;
        $this->page = $page;
    }

    /**
     * Get total number of items
     * @return int
     */
    public function total(): int
    {
        return $this->total;
    }

    /**
     * Get current page number
     * @return int
     */
    public function currentPageNumber(): int
    {
        return $this->page;
    }

    /**
     * Get previous page number
     * @return int|null
     */
    public function previousPageNumber(): ?int
    {
        if ($this->page > 1) {
            return $this->page - 1;
        }

        if ($this->page == 1) {
            return $this->page;
        }

        return null;
    }

    /**
     * Get next page number
     * @return int|null
     */
    public function nextPageNumber(): ?int
    {
        if ($this->page < $this->lastPageNumber()) {
            return $this->page + 1;
        }

        if ($this->page == $this->lastPageNumber()) {
            return $this->page;
        }

        return null;
    }

    /**
     * Get last page number
     * @return int
     */
    public function lastPageNumber(): int
    {
        return (int)ceil($this->total() / $this->perPage);
    }

    /**
     * Get current page link
     * @param bool $withBaseUrl
     * @return string|null
     */
    public function currentPageLink(bool $withBaseUrl = false): ?string
    {
        return $this->getPageLink($this->page, $withBaseUrl);
    }

    /**
     * Get first page link
     * @param bool $withBaseUrl
     * @return string|null
     */
    public function firstPageLink(bool $withBaseUrl = false): ?string
    {
        return $this->getPageLink(Pagination::FIRST_PAGE_NUMBER, $withBaseUrl);
    }

    /**
     * Get previous page link
     * @param bool $withBaseUrl
     * @return string|null
     */
    public function previousPageLink(bool $withBaseUrl = false): ?string
    {
        return $this->getPageLink($this->previousPageNumber(), $withBaseUrl);
    }

    /**
     * Get next page link
     * @param bool $withBaseUrl
     * @return string|null
     */
    public function nextPageLink(bool $withBaseUrl = false): ?string
    {
        return $this->getPageLink($this->nextPageNumber(), $withBaseUrl);
    }

    /**
     * Get last page link
     * @param bool $withBaseUrl
     * @return string|null
     */
    public function lastPageLink(bool $withBaseUrl = false): ?string
    {
        return $this->getPageLink($this->lastPageNumber(), $withBaseUrl);
    }

    /**
     * Get items per page
     * @return int
     */
    public function perPage(): int
    {
        return $this->perPage;
    }

    /**
     * Get all page links
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
     * Get pagination HTML
     * @param bool $withBaseUrl
     * @param int|null $pageItemsCount
     * @return string|null
     */
    public function getPagination(bool $withBaseUrl = false, ?int $pageItemsCount = null): ?string
    {
        $totalPages = $this->lastPageNumber();
        $currentPage = $this->currentPageNumber();

        if ($totalPages <= 1) {
            return null;
        }

        $pageItemsCount = max(Pagination::MINIMUM_PAGE_ITEMS_COUNT, $pageItemsCount ?? Pagination::MINIMUM_PAGE_ITEMS_COUNT);

        $pagination = ['<ul class="' . Pagination::PAGINATION_CLASS . '">'];

        if ($currentPage > 1) {
            $pagination[] = $this->getPreviousPageItem($this->previousPageLink());
        }

        if ($pageItemsCount) {
            $links = $this->links($withBaseUrl);

            [$startPage, $endPage] = $this->calculateStartEndPages($currentPage, $totalPages, $pageItemsCount);

            $pagination[] = $this->addFirstPageLink($startPage);
            $pagination[] = $this->getItemsLinks($startPage, $endPage, $currentPage, $links);
            $pagination[] = $this->addLastPageLink($endPage, $totalPages, $links);
        }

        if ($currentPage < $totalPages) {
            $pagination[] = $this->getNextPageItem($this->nextPageLink());
        }

        $pagination[] = '</ul>';

        return implode('', $pagination);
    }

    /**
     * Get the URI for pagination
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

    /**
     * Get page link
     * @param int|null $pageNumber
     * @param bool $withBaseUrl
     * @return string|null
     */
    protected function getPageLink(?int $pageNumber, bool $withBaseUrl = false): ?string
    {
        if ($pageNumber !== null && $pageNumber !== 0) {
            return $this->getUri($withBaseUrl) . Pagination::PER_PAGE . '=' . $this->perPage . '&' . Pagination::PAGE . '=' . $pageNumber;
        }

        return null;
    }

    /**
     * Get next page item HTML
     * @param string|null $nextPageLink
     * @return string
     */
    protected function getNextPageItem(?string $nextPageLink): string
    {
        $link = '';

        if (!in_array($nextPageLink, [null, '', '0'], true)) {
            $link = '<li><a href="' . $nextPageLink . '">' . t('common.pagination.next') . '</a></li>';
        }

        return $link;
    }

    /**
     * Get previous page item HTML
     * @param string|null $previousPageLink
     * @return string
     */
    protected function getPreviousPageItem(?string $previousPageLink): string
    {
        $link = '';

        if (!in_array($previousPageLink, [null, '', '0'], true)) {
            $link = '<li><a href="' . $previousPageLink . '">' . t('common.pagination.prev') . '</a></li>';
        }

        return $link;
    }

    /**
     * Get items links HTML
     * @param int $startPage
     * @param int $endPage
     * @param int $currentPage
     * @param array $links
     * @return string
     */
    protected function getItemsLinks(int $startPage, int $endPage, int $currentPage, array $links): string
    {
        $pagination = [];

        for ($i = $startPage; $i <= $endPage; $i++) {
            $active = $i === $currentPage ? 'class="' . Pagination::PAGINATION_CLASS_ACTIVE . '"' : '';
            $pagination .= '<li ' . $active . '><a href="' . $links[$i - 1] . '">' . $i . '</a></li>';
        }

        return $pagination;
    }

    /**
     * Calculate start and end pages
     * @param int $currentPage
     * @param int $totalPages
     * @param int $pageItemsCount
     * @return array
     */
    protected function calculateStartEndPages(int $currentPage, int $totalPages, int $pageItemsCount): array
    {
        $startPage = max(1, $currentPage - ceil(($pageItemsCount - Pagination::EDGE_PADDING) / 2));
        $endPage = min($totalPages, $startPage + $pageItemsCount - Pagination::EDGE_PADDING);

        return [$startPage, $endPage];
    }

    /**
     * Add first page link HTML
     * @param int $startPage
     * @return string
     */
    protected function addFirstPageLink(int $startPage): string
    {
        $pagination = '';

        if ($startPage > 1) {
            $pagination .= '<li><a href="' . $this->firstPageLink() . '">' . Pagination::FIRST_PAGE_NUMBER . '</a></li>';
            if ($startPage > 2) {
                $pagination .= '<li><span>...</span></li>';
            }
        }

        return $pagination;
    }

    /**
     * Add last page link HTML
     * @param int $endPage
     * @param int $totalPages
     * @param array $links
     * @return string
     */
    protected function addLastPageLink(int $endPage, int $totalPages, array $links): string
    {
        $pagination = '';

        if ($endPage < $totalPages) {
            if ($endPage < $totalPages - 1) {
                $pagination .= '<li><span>...</span></li>';
            }
            $pagination .= '<li><a href="' . $links[$totalPages - 1] . '">' . $totalPages . '</a></li>';
        }

        return $pagination;
    }
}
