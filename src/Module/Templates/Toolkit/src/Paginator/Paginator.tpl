<?php

namespace Modules\Toolkit\Paginator;

use Quantum\Paginator\Contracts\PaginatorInterface;
use Quantum\Paginator\Traits\PaginatorTrait;


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

    public static function fromArray(array $params): PaginatorInterface
    {
        return new self(
            $params['items'],
            $params['total'],
            $params['perPage'] ?? 10,
            $params['page'] ?? 1,
            $params['isSliced'] ?? false
        );
    }

    /**
     * @inheritDoc
     */
    public function firstItem()
    {
        $data = $this->data();

        if (empty($data)) {
            return null;
        }

        return reset($data);
    }

    /**
     * @inheritDoc
     */
    public function lastItem()
    {
        $data = $this->data();

        if (empty($data)) {
            return null;
        }

        return end($data);
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->data;
    }
}