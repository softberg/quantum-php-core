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
use Quantum\App\Exceptions\BaseException;
use Quantum\Model\ModelCollection;
use Quantum\Model\DbModel;

/**
 * Class ModelPaginator
 * @package Quantum\Paginator
 */
class ModelPaginator implements PaginatorInterface
{
    use PaginatorTrait;

    /**
     * @var string
     */
    private string $modelClass;

    /**
     * @var DbModel
     */
    private DbModel $model;

    /**
     * @param DbModel $model
     * @param int $perPage
     * @param int $page
     */
    public function __construct(DbModel $model, int $perPage, int $page = 1)
    {
        $this->initialize($perPage, $page);

        $this->model = $model;
        $this->modelClass = $model->getModelName();
        $this->total = $model->count();
    }

    /**
     * @inheritDoc
     * @throws BaseException
     */
    public function data(): ModelCollection
    {
        $result = $this->model
            ->limit($this->perPage)
            ->offset($this->perPage * ($this->page - 1))
            ->get();

        if ($this->modelClass != '@anonymous') {
            $result = array_map(fn ($item) => wrapToModel($item->getOrmInstance(), $this->modelClass), iterator_to_array($result));
        }

        return new ModelCollection($result);
    }

    /**
     * @inheritDoc
     * @throws BaseException
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
     * @throws BaseException
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
