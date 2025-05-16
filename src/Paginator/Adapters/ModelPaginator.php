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
 * @since 2.9.7
 */

namespace Quantum\Paginator\Adapters;

use Quantum\Libraries\Database\Contracts\DbalInterface;
use Quantum\Paginator\Contracts\PaginatorInterface;
use Quantum\Paginator\Traits\PaginatorTrait;
use Quantum\Model\ModelCollection;

/**
 * Class ModelPaginator
 * @package Quantum\Paginator
 */
class ModelPaginator implements PaginatorInterface
{

    use PaginatorTrait;

    /**
     * @var DbalInterface
     */
    private $ormInstance;

    /**
     * @var string
     */
    private $modelClass;

    /**
     * @param DbalInterface $ormInstance
     * @param string $modelClass
     * @param int $perPage
     * @param int $page
     */
    public function __construct(DbalInterface $ormInstance, string $modelClass, int $perPage, int $page = 1)
    {
        $this->initialize($perPage, $page);
        $this->ormInstance = $ormInstance;
        $this->modelClass = $modelClass;
        $this->total = $this->ormInstance->count();
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $params): PaginatorInterface
    {
        return new self(
            $params['orm'],
            $params['model'],
            $params['perPage'] ?? 10,
            $params['page'] ?? 1
        );
    }

    /**
     * @inheritDoc
     */
    public function data(): ModelCollection
    {
        $ormInstances = $this->ormInstance
            ->limit($this->perPage)
            ->offset($this->perPage * ($this->page - 1))
            ->get();

        $models = array_map(function ($item) {
            return wrapToModel($item, $this->modelClass);
        }, $ormInstances);

        return new ModelCollection($models);
    }

    /**
     * @inheritDoc
     */
    public function firstItem()
    {
        $data = $this->data();

        if ($data->isEmpty()) {
            return null;
        }

        return $data->first();
    }

    /**
     * @inheritDoc
     */
    public function lastItem()
    {
        $data = $this->data();

        if ($data->isEmpty()) {
            return null;
        }

        return $data->last();
    }
} 