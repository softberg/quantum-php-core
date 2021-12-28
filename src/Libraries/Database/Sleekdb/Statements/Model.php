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
 * @since 2.6.0
 */

namespace Quantum\Libraries\Database\Sleekdb\Statements;

use Quantum\Libraries\Database\DbalInterface;

/**
 * Trait Model
 * @package Quantum\Libraries\Database\Sleekdb\Statements
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
        if (!is_null($value)) {
            $this->modifiedFields[$key] = $value;
        } else {
            return $this->modifiedFields[$key] ?? null;
        }
    }

    /**
     * @inheritDoc
     * @throws \Quantum\Exceptions\DatabaseException
     * @throws \SleekDB\Exceptions\IOException
     * @throws \SleekDB\Exceptions\IdNotAllowedException
     * @throws \SleekDB\Exceptions\InvalidArgumentException
     * @throws \SleekDB\Exceptions\InvalidConfigurationException
     * @throws \SleekDB\Exceptions\JsonException
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
     * @throws \Quantum\Exceptions\DatabaseException
     * @throws \SleekDB\Exceptions\IOException
     * @throws \SleekDB\Exceptions\InvalidArgumentException
     * @throws \SleekDB\Exceptions\InvalidConfigurationException
     */
    public function delete(): bool
    {
        $pk = $this->getOrmModel()->getPrimaryKey();
        return $this->getOrmModel()->deleteById($this->data[$pk]);
    }

    /**
     * @inheritDoc
     * @throws \Quantum\Exceptions\DatabaseException
     * @throws \SleekDB\Exceptions\IOException
     * @throws \SleekDB\Exceptions\InvalidArgumentException
     * @throws \SleekDB\Exceptions\InvalidConfigurationException
     */
    public function deleteMany(): bool
    {
        return $this->getBuilder()->getQuery()->delete();
    }
}