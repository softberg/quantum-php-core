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
 * @since 2.9.8
 */

namespace Quantum\Paginator\Contracts;

/**
 * Paginator interface
 * @package Quantum\Paginator
 */
interface PaginatorInterface
{

    /**
     * Get the paginated data
     * @return mixed
     */
    public function data();

    /**
     * Get the first item of the current page
     * @return mixed
     */
    public function firstItem();

    /**
     * Get the last item of the current page
     * @return mixed
     */
    public function lastItem();

    /**
     * Get current page number
     * @return int
     */
    public function currentPageNumber(): int;

    /**
     * Get previous page number
     * @return int|null
     */
    public function previousPageNumber(): ?int;

    /**
     * Get next page number
     * @return int|null
     */
    public function nextPageNumber(): ?int;

    /**
     * Get last page number
     * @return int
     */
    public function lastPageNumber(): int;

    /**
     * Get current page link
     * @param bool $withBaseUrl
     * @return string|null
     */
    public function currentPageLink(bool $withBaseUrl = false): ?string;

    /**
     * Get first page link
     * @param bool $withBaseUrl
     * @return string|null
     */
    public function firstPageLink(bool $withBaseUrl = false): ?string;

    /**
     * Get previous page link
     * @param bool $withBaseUrl
     * @return string|null
     */
    public function previousPageLink(bool $withBaseUrl = false): ?string;

    /**
     * Get next page link
     * @param bool $withBaseUrl
     * @return string|null
     */
    public function nextPageLink(bool $withBaseUrl = false): ?string;

    /**
     * Get last page link
     * @param bool $withBaseUrl
     * @return string|null
     */
    public function lastPageLink(bool $withBaseUrl = false): ?string;

    /**
     * Get items per page
     * @return int
     */
    public function perPage(): int;

    /**
     * Get total items count
     * @return int
     */
    public function total(): int;

    /**
     * Get all page links
     * @param bool $withBaseUrl
     * @return array
     */
    public function links(bool $withBaseUrl = false): array;

    /**
     * Get pagination HTML
     * @param bool $withBaseUrl
     * @param int|null $pageItemsCount
     * @return string|null
     */
    public function getPagination(bool $withBaseUrl = false, int $pageItemsCount = null): ?string;
}