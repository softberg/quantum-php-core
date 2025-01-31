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

namespace Quantum\Libraries\Database\Adapters\Sleekdb;

use Quantum\Libraries\Database\Contracts\PaginatorInterface;
use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Database\Exceptions\ModelException;
use Quantum\Libraries\Database\Traits\PaginatorTrait;
use SleekDB\Exceptions\InvalidConfigurationException;
use SleekDB\Exceptions\InvalidArgumentException;
use SleekDB\Exceptions\IOException;

/**
 * Class Paginator
 * @package Quantum\Libraries\Database
 */
class Paginator implements PaginatorInterface
{

    use PaginatorTrait;

    /**
     * @var SleekDbal
     */
    private $dbal;

    /**
     * @var array
     */
    public $data;

    /**
     * @param $sleekDbal
     * @param int $perPage
     * @param int $page
     * @throws DatabaseException
     * @throws ModelException
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     */
    public function __construct($sleekDbal, int $perPage, int $page = 1)
    {
        $this->total = count($sleekDbal->getBuilder()->getQuery()->fetch());
        $this->dbal = $sleekDbal;
        $this->dbal->limit($perPage)->offset($perPage * ($page - 1));
        $this->data = $this->dbal->getBuilder()->getQuery()->fetch();
        $this->perPage = $perPage;
        $this->page = $page;
        $this->baseUrl = base_url();
    }

    /**
     * @inheritDoc
     */
    public function firstItem()
    {
        return $this->data[array_key_first($this->data)];
    }

    /**
     * @inheritDoc
     */
    public function lastItem()
    {
        return $this->data[array_key_last($this->data)];
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        return array_map(function ($element) {
            $item = clone $this->dbal;
            $item->setData($element);
            $item->setModifiedFields($element);
            $item->setIsNew(false);
            return $item;
        }, $this->data) ?? [];
    }
}