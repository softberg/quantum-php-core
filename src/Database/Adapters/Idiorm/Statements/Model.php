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

namespace Quantum\Database\Adapters\Idiorm\Statements;

use Quantum\Database\Exceptions\DatabaseException;
use Quantum\Database\Contracts\DbalInterface;

/**
 * Trait Model
 * @package Quantum\Database
 */
trait Model
{
    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function create(): DbalInterface
    {
        $this->getOrmModel()->create();
        return $this;
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function prop(string $key, $value = null)
    {
        if (func_num_args() === 2) {
            $this->getOrmModel()->$key = $value;
        } else {
            return $this->getOrmModel()->$key ?? null;
        }
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function save(): bool
    {
        return $this->getOrmModel()->save();
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function delete(): bool
    {
        return $this->getOrmModel()->delete();
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function deleteMany(): bool
    {
        return $this->getOrmModel()->delete_many();
    }
}
