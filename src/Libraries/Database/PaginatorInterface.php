<?php

namespace Quantum\Libraries\Database;

interface PaginatorInterface
{
	public function __construct($dbal, int $per_page, int $page);
	
	public function getPagination(bool $withBaseUrl): ?string;

	public function currentPageNumber(): int;

	public function currentPageLink(): ?string;

	public function previousPageLink(): ?string;

	public function previousPageNumber(): ?int;

	public function firstPageLink(): ?string;

	public function nextPageLink(): ?string;

	public function nextPageNumber(): ?int;

	public function lastPageLink(): ?string;
	
	public function lastPageNumber();

	public function firstItem();

	public function lastItem();

	public function perPage();

	public function total();

	public function links(bool $withBaseUrl);

	public function data();
}
