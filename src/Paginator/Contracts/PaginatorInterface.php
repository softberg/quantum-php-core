<?php

declare(strict_types=1);

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
     */
    public function currentPageNumber(): int;

    /**
     * Get previous page number
     */
    public function previousPageNumber(): ?int;

    /**
     * Get next page number
     */
    public function nextPageNumber(): ?int;

    /**
     * Get last page number
     */
    public function lastPageNumber(): int;

    /**
     * Get current page link
     */
    public function currentPageLink(bool $withBaseUrl = false): ?string;

    /**
     * Get first page link
     */
    public function firstPageLink(bool $withBaseUrl = false): ?string;

    /**
     * Get previous page link
     */
    public function previousPageLink(bool $withBaseUrl = false): ?string;

    /**
     * Get next page link
     */
    public function nextPageLink(bool $withBaseUrl = false): ?string;

    /**
     * Get last page link
     */
    public function lastPageLink(bool $withBaseUrl = false): ?string;

    /**
     * Get items per page
     */
    public function perPage(): int;

    /**
     * Get total items count
     */
    public function total(): int;

    /**
     * Get all page links
     */
    public function links(bool $withBaseUrl = false): array;

    /**
     * Get pagination HTML
     */
    public function getPagination(bool $withBaseUrl = false, ?int $pageItemsCount = null): ?string;
}
