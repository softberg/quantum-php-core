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

namespace Quantum\Paginator\Adapters;

use Quantum\Paginator\Contracts\PaginatorInterface;
use Quantum\Paginator\Traits\PaginatorTrait;

/**
 * Class ArrayPaginator
 * @package Quantum\Paginator
 */
class ArrayPaginator implements PaginatorInterface
{
    use PaginatorTrait;

    /**
     * @var array
     */
    private $items;

    /**
     * @param array $items
     * @param int $perPage
     * @param int $page
     */
    public function __construct(array $items, int $perPage, int $page = 1)
    {
        $this->initialize($perPage, $page);

        $this->items = $items;
        $this->total = count($items);
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        return array_slice(
            $this->items,
            ($this->page - 1) * $this->perPage,
            $this->perPage
        );
    }

    /**
     * @inheritDoc
     */
    public function firstItem()
    {
        $data = $this->data();

        if ($data === []) {
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

        if ($data === []) {
            return null;
        }

        return end($data);
    }
}
