<?php

namespace Quantum\Libraries\Database;

interface PaginatorInterface
{
	public function getPagination(bool $withBaseUrl): ?string;

	public function currentPageNumber(): int;

	public function currentPageLink(bool $withBaseUrl): ?string;

	public function previousPageLink(bool $withBaseUrl): ?string;

	public function previousPageNumber(): ?int;

	public function firstPageLink(bool $withBaseUrl): ?string;

	public function nextPageLink(bool $withBaseUrl): ?string;

	public function nextPageNumber(): ?int;

	public function lastPageLink(bool $withBaseUrl): ?string;
	
	public function lastPageNumber();

	public function firstItem();

	public function lastItem();

	public function perPage();

	public function total();

	public function links(bool $withBaseUrl);

	public function data();
}
