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

namespace Quantum\Libraries\Database;

/**
 * Class BasePaginator
 * @package Quantum\Libraries\Database
 */
abstract class BasePaginator implements PaginatorInterface
{
    /**
     * Pagination class name
     */
    protected const PAGINATION_CLASS = 'pagination';

    /**
     * Active class name
     */
    protected const PAGINATION_CLASS_ACTIVE = 'active';

    /**
     * Parameter name for per page
     */
    protected const PER_PAGE = 'per_page';

    /**
     * Parameter name for page number
     */
    protected const PAGE = 'page';

    /**
     * First page number
     */
    protected const FIRST_PAGE_NUMBER = 1;

    /**
     * Minimum page items count
     */
    protected const MINIMUM_PAGE_ITEMS_COUNT = 3;

    /**
     * Edge padding
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
     * @inheritDoc
     */
    public function currentPageNumber(): int
    {
        return $this->page;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
     */
    public function lastPageNumber(): int
    {
        return (int)ceil($this->total() / $this->perPage);
    }

    /**
     * @inheritDoc
     */
    public function currentPageLink(bool $withBaseUrl = false): ?string
    {
        return $this->getPageLink($this->page, $withBaseUrl);
    }

    /**
     * @inheritDoc
     */
    public function firstPageLink(bool $withBaseUrl = false): ?string
    {
        return $this->getPageLink(self::FIRST_PAGE_NUMBER, $withBaseUrl);
    }

    /**
     * @inheritDoc
     */
    public function previousPageLink(bool $withBaseUrl = false): ?string
    {
        return $this->getPageLink($this->previousPageNumber(), $withBaseUrl);
    }

    /**
     * @inheritDoc
     */
    public function nextPageLink(bool $withBaseUrl = false): ?string
    {
        return $this->getPageLink($this->nextPageNumber(), $withBaseUrl);
    }

    /**
     * @inheritDoc
     */
    public function lastPageLink(bool $withBaseUrl = false): ?string
    {
        return $this->getPageLink($this->lastPageNumber(), $withBaseUrl);
    }

    /**
     * @inheritDoc
     */
    public function perPage(): int
    {
        return $this->perPage;
    }

    /**
     * @inheritDoc
     */
    public function total(): int
    {
        return $this->total;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
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

        $pagination = '<ul class="' . self::PAGINATION_CLASS . '">';
        $currentPage = $this->currentPageNumber();

        if ($currentPage > 1) {
            $pagination .= $this->getPreviousPageItem($this->previousPageLink());
        }

        if ($pageItemsCount) {
            $links = $this->links($withBaseUrl);
            list($startPage, $endPage) = $this->calculateStartEndPages($currentPage, $totalPages, $pageItemsCount);
            $pagination .= $this->addFirstPageLink($startPage);
            $pagination .= $this->getItemsLinks($startPage, $endPage, $currentPage, $links);
            $pagination .= $this->addLastPageLink($endPage, $totalPages, $links);
        }

        if ($currentPage < $totalPages) {
            $pagination .= $this->getNextPageItem($this->nextPageLink());
        }

        $pagination .= '</ul>';

        return $pagination;
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

    /**
     * @param $pageNumber
     * @param bool $withBaseUrl
     * @return string|null
     */
    protected function getPageLink($pageNumber, bool $withBaseUrl = false): ?string
    {
        if (!empty($pageNumber)) {
            return $this->getUri($withBaseUrl) . self::PER_PAGE . '=' . $this->perPage . '&' . self::PAGE . '=' . $pageNumber;
        }

        return null;
    }

    /**
     * @param string|null $nextPageLink
     * @return string
     */
    protected function getNextPageItem(?string $nextPageLink): string
    {
        $link = '';
        if (!empty($nextPageLink)) {
            $link = '<li><a href="' . $nextPageLink . '">' . t('common.pagination.next') . '</a></li>';
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
        if (!empty($previousPageLink)) {
            $link = '<li><a href="' . $previousPageLink . '">' . t('common.pagination.prev') . '</a></li>';
        }
        return $link;
    }

    /**
     * @param $startPage
     * @param $endPage
     * @param $currentPage
     * @param array $links
     * @return string
     */
    protected function getItemsLinks($startPage, $endPage, $currentPage, array $links): string
    {
        $pagination = '';
        for ($i = $startPage; $i <= $endPage; $i++) {
            $active = $i == $currentPage ? 'class="' . self::PAGINATION_CLASS_ACTIVE . '"' : '';
            $pagination .= '<li ' . $active . '><a href="' . $links[$i - 1] . '">' . $i . '</a></li>';
        }
        return $pagination;
    }

    /**
     * @param $currentPage
     * @param $totalPages
     * @param $pageItemsCount
     * @return array
     */
    protected function calculateStartEndPages($currentPage, $totalPages, $pageItemsCount): array
    {
        $startPage = max(1, $currentPage - ceil(($pageItemsCount - self::EDGE_PADDING) / 2));
        $endPage = min($totalPages, $startPage + $pageItemsCount - self::EDGE_PADDING);

        return [$startPage, $endPage];
    }

    /**
     * @param $startPage
     * @return string
     */
    protected function addFirstPageLink($startPage): string
    {
        $pagination = '';
        if ($startPage > 1) {
            $pagination .= '<li><a href="' . $this->firstPageLink() . '">' . self::FIRST_PAGE_NUMBER . '</a></li>';
            if ($startPage > 2) {
                $pagination .= '<li><span>...</span></li>';
            }
        }

        return $pagination;
    }

    /**
     * @param $endPage
     * @param $totalPages
     * @param $links
     * @return string
     */
    protected function addLastPageLink($endPage, $totalPages, $links): string
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