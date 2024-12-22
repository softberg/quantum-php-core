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
 * Paginator interface
 * @package Quantum\Libraries\Database
 */
interface PaginatorInterface
{

    /**
     * @return int
     */
	public function currentPageNumber(): int;

    /**
     * @return int|null
     */
	public function previousPageNumber(): ?int;

    /**
     * @return int|null
     */
	public function nextPageNumber(): ?int;

    /**
     * @return int
     */
    public function lastPageNumber(): int;

    /**
     * @param bool $withBaseUrl
     * @return string|null
     */
    public function currentPageLink(bool $withBaseUrl): ?string;

    /**
     * @param bool $withBaseUrl
     * @return string|null
     */
    public function firstPageLink(bool $withBaseUrl): ?string;

    /**
     * @param bool $withBaseUrl
     * @return string|null
     */
    public function previousPageLink(bool $withBaseUrl): ?string;

    /**
     * @param bool $withBaseUrl
     * @return string|null
     */
    public function nextPageLink(bool $withBaseUrl): ?string;

    /**
     * @param bool $withBaseUrl
     * @return string|null
     */
    public function lastPageLink(bool $withBaseUrl): ?string;

    /**
     * @return int
     */
	public function perPage(): int;

    /**
     * @return int
     */
	public function total(): int;

    /**
     * @param bool $withBaseUrl
     * @return array
     */
	public function links(bool $withBaseUrl): array;

    /**
     * @param bool $withBaseUrl
     * @param null $pageItemsCount
     * @return string|null
     */
    public function getPagination(bool $withBaseUrl, $pageItemsCount = null): ?string;

    /**
     * @return mixed
     */
    public function firstItem();

    /**
     * @return mixed
     */
    public function lastItem();

    /**
     * @return array
     */
	public function data(): array;
}
