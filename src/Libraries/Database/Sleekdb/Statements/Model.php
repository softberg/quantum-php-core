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

namespace Quantum\Libraries\Database\Sleekdb\Statements;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\Libraries\Database\Exceptions\ModelException;
use Quantum\Libraries\Database\Contracts\DbalInterface;
use SleekDB\Exceptions\InvalidConfigurationException;
use SleekDB\Exceptions\InvalidArgumentException;
use SleekDB\Exceptions\IdNotAllowedException;
use SleekDB\Exceptions\JsonException;
use SleekDB\Exceptions\IOException;

/**
 * Trait Model
 * @package Quantum\Libraries\Database
 */
trait Model
{

    /**
     * @inheritDoc
     */
    public function create(): DbalInterface
    {
        $this->isNew = true;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function prop(string $key, $value = null)
    {
        if (func_num_args() == 2) {
            $this->modifiedFields[$key] = $value;
        } else {
            return $this->modifiedFields[$key] ?? null;
        }
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     * @throws IOException
     * @throws IdNotAllowedException
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     * @throws JsonException
     */
    public function save(): bool
    {
        $ormMode = $this->getOrmModel();

        if ($this->isNew) {
            $ormMode->insert($this->modifiedFields);
        } else {
            $ormMode->update($this->modifiedFields);
        }

        return true;
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     */
    public function delete(): bool
    {
        $pk = $this->getOrmModel()->getPrimaryKey();
        return $this->getOrmModel()->deleteById($this->data[$pk]);
    }

    /**
     * @inheritDoc
     * @throws DatabaseException
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     * @throws ModelException
     */
    public function deleteMany(): bool
    {
        return $this->getBuilder()->getQuery()->delete();
    }
}