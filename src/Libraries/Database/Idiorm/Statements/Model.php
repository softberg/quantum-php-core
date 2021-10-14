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

namespace Quantum\Libraries\Database\Idiorm\Statements;

/**
 * Trait Model
 * @package Quantum\Libraries\Database\Idiorm\Statements
 */
trait Model
{

    /**
     * @inheritDoc
     */
    public function get(?int $returnType = self::TYPE_ARRAY)
    {
        return ($returnType == self::TYPE_OBJECT) ? $this->getOrmModel()->find_many() : $this->getOrmModel()->find_array();
    }

    /**
     * @inheritDoc
     */
    public function create(): object
    {
        return $this->getOrmModel()->create();
    }

    /**
     * @inheritDoc
     */
    public function save(): bool
    {
        return $this->getOrmModel()->save();
    }

    /**
     * @inheritDoc
     */
    public function delete(): bool
    {
        return $this->getOrmModel()->delete();
    }

}