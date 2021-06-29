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
 * @since 2.4.0
 */
namespace Quantum\Libraries\Database\Statements;

/**
 * Trait Model
 * @package Quantum\Libraries\Database\Statements
 */
trait Model
{
    /**
     * Gets the result set
     * @inheritDoc
     */
    public function get(?int $returnType = self::TYPE_ARRAY)
    {
        return ($returnType == self::TYPE_OBJECT) ? $this->ormObject->find_many() : $this->ormObject->find_array();
    }

    /**
     * Creates new db record
     * @inheritDoc
     */
    public function create(): object
    {
        return $this->ormObject->create();
    }

    /**
     * Saves the data into the database
     * @inheritDoc
     */
    public function save(): bool
    {
        return $this->ormObject->save();
    }

    /**
     * Deletes the record from the database
     * @inheritDoc
     */
    public function delete(): bool
    {
        return $this->ormObject->delete();
    }

}