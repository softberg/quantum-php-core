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

namespace Quantum\Libraries\Database;

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
    private $ormObject;

    /**
     * @var object
     */
    private static $instance = null;

    private function __construct()
    {
    }

    /**
     * Get Instance
     * @return IdiormPatch
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Set ORM Object
     * @param $ormObject
     * @return $this
     */
    public function setOrmObject($ormObject): IdiormPatch
    {
        $this->ormObject = $ormObject;
        return $this;
    }

    /**
     * Add an LEFT JOIN source to the query
     * @param string $table
     * @param array $constraint
     * @param string|null $table_alias
     * @return object
     */
    public function left_join(string $table, array $constraint, string $table_alias = null): object
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
    public function right_join(string $table, array $constraint, string $table_alias = null): object
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
        return $this->ormObject->_add_join_source($operator, $table, $constraint, $table_alias);
    }

}
