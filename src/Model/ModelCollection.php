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

namespace Quantum\Model;

use Quantum\Model\Exceptions\ModelException;
use Quantum\App\Exceptions\BaseException;
use IteratorAggregate;
use Countable;
use Generator;

/**
 * Class ModelCollection
 * @package Quantum\Model
 */
class ModelCollection implements Countable, IteratorAggregate
{
    /**
     * @var QtModel[]
     */
    private $models = [];

    /**
     * @var iterable
     */
    private $originalModels;

    /**
     * @var bool
     */
    private $modelsProcessed = false;

    /**
     * @param iterable $models
     * @throws BaseException
     */
    public function __construct(iterable $models = [])
    {
        $this->originalModels = $models;

        if (is_array($models)) {
            $this->processModels();
        }
    }

    /**
     * Add a model to the collection
     * @param QtModel $model
     * @return self
     * @throws BaseException
     */
    public function add(QtModel $model): self
    {
        $this->processModels();

        $this->models[] = $model;

        if (!is_array($this->originalModels)) {
            $this->originalModels = $this->models;
        } else {
            $this->originalModels[] = $model;
        }

        return $this;
    }

    /**
     * Remove a model from the collection
     * @param QtModel $model
     * @return self
     * @throws BaseException
     */
    public function remove(QtModel $model): self
    {
        $this->processModels();

        $this->models = array_filter($this->models, fn ($m) => $m !== $model);

        $this->originalModels = $this->models;

        return $this;
    }

    /**
     * Get all models as an array
     * @return QtModel[]
     * @throws BaseException
     */
    public function all(): array
    {
        $this->processModels();
        return $this->models;
    }

    /**
     * Get the count of models in the collection
     * @return int
     * @throws BaseException
     */
    public function count(): int
    {
        $this->processModels();
        return count($this->models);
    }

    /**
     * Get the first model in the collection
     * @return QtModel|null
     * @throws BaseException
     */
    public function first(): ?QtModel
    {
        foreach ($this->getIterator() as $model) {
            return $model;
        }

        return null;
    }

    /**
     * Get the last model in the collection
     * @return QtModel|null
     * @throws BaseException
     */
    public function last(): ?QtModel
    {
        $this->processModels();
        return empty($this->models) ? null : end($this->models);
    }

    /**
     * Check if the collection is empty
     * @return bool
     */
    public function isEmpty(): bool
    {
        return !$this->first() instanceof \Quantum\Model\QtModel;
    }

    /**
     * Get an iterator for the collection
     * @return Generator
     * @throws BaseException
     */
    public function getIterator(): Generator
    {
        if ($this->modelsProcessed) {
            yield from $this->models;
        } else {
            foreach ($this->originalModels as $model) {
                $this->validateModel($model);
                yield $model;
            }

            $this->processModels();
        }
    }

    /**
     * Process models from original source into the internal array
     * @throws BaseException
     */
    private function processModels()
    {
        if ($this->modelsProcessed) {
            return;
        }

        $this->models = [];

        foreach ($this->originalModels as $model) {
            $this->validateModel($model);
            $this->models[] = $model;
        }

        $this->modelsProcessed = true;
    }

    /**
     * Validate that an item is a QtModel instance
     * @param $model
     * @return void
     * @throws BaseException
     */
    private function validateModel($model): void
    {
        if (!$model instanceof QtModel) {
            throw ModelException::notInstanceOf(get_class($model), QtModel::class);
        }
    }
}
