<?php

namespace Modules\Toolkit\Paginator;


use Quantum\Libraries\Database\Contracts\PaginatorInterface;
use Quantum\Libraries\Database\Traits\PaginatorTrait;

class Paginator implements PaginatorInterface
{
    use PaginatorTrait;

    /**
     * @var array
     */
    private $data;

    /**
     * @param $data
     * @param int $total
     * @param int $perPage
     * @param int $page
     */
    public function __construct($data, int $total, int $perPage, int $page = 1)
    {
        $this->data = $data;
        $this->perPage = $perPage;
        $this->page = $page;
        $this->total = $total;
        $this->baseUrl = base_url();
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
     * @return array
     */
    public function data(): array
    {
        return $this->data ? array_slice($this->data, $this->perPage * $this->page - $this->perPage, $this->perPage) : [];
    }
}