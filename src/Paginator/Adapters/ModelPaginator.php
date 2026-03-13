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

namespace Quantum\Paginator\Adapters;

use Quantum\Paginator\Contracts\PaginatorInterface;
use Quantum\Paginator\Traits\PaginatorTrait;
use Quantum\App\Exceptions\BaseException;
use Quantum\Model\ModelCollection;
use Quantum\Model\DbModel;
use Quantum\Model\Model;

/**
 * Class ModelPaginator
 * @package Quantum\Paginator
 */
class ModelPaginator implements PaginatorInterface
{
    use PaginatorTrait;

    private string $modelClass;

    private DbModel $model;

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
            $result = array_map(fn ($item): ?DbModel => wrapToModel($item->getOrmInstance(), $this->modelClass), iterator_to_array($result));
        }

        return new ModelCollection($result);
    }

    /**
     * @inheritDoc
     * @throws BaseException
     */
    public function firstItem(): ?Model
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
    public function lastItem(): ?Model
    {
        $data = $this->data();

        if ($data->isEmpty()) {
            return null;
        }

        return $data->last();
    }
}
