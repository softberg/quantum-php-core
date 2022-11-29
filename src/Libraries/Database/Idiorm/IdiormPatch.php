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
 * @since 2.8.0
 */

namespace Quantum\Libraries\Database\Idiorm;

use ORM;

/**
 * Class IdiormPatch
 * @package Quantum\Libraries\Database
 */
class IdiormPatch extends ORM
{

    /**
     * @var object
     */
    private $ormModel;

    /**
     * @var object
     */
    private static $instance = null;

    /**
     * Get Instance
     * @return IdiormPatch|object|null
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self('dummy');
        }

        return self::$instance;
    }

    /**
     * Set ORM Object
     * @param object $ormModel
     * @return $this
     */
    public function use(object $ormModel): IdiormPatch
    {
        $this->ormModel = $ormModel;
        return $this;
    }

    /**
     * Add an LEFT JOIN source to the query
     * @param string $table
     * @param array $constraint
     * @param string|null $table_alias
     * @return object
     */
    public function leftJoin(string $table, array $constraint, string $table_alias = null): object
    {
        return $this->addJoin("LEFT", $table, $constraint, $table_alias);
    }

    /**
     * Add an RIGHT JOIN source to the query
     * @param string $table
     * @param array $constraint
     * @param string|null $table_alias
     * @return object
     */
    public function rightJoin(string $table, array $constraint, string $table_alias = null): object
    {
        return $this->addJoin("RIGHT", $table, $constraint, $table_alias);
    }

    /**
     * Add Join
     * @param string $operator
     * @param string $table
     * @param array $constraint
     * @param string|null $table_alias
     * @return object
     */
    public function addJoin(string $operator, string $table, array $constraint, string $table_alias = null): object
    {
        return $this->ormModel->_add_join_source($operator, $table, $constraint, $table_alias);
    }

}
