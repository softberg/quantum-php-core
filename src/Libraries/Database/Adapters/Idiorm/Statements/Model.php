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

namespace Quantum\Libraries\Database\Adapters\Idiorm\Statements;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Database\Contracts\DbalInterface;

/**
 * Trait Model
 * @package Quantum\Libraries\Database
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
