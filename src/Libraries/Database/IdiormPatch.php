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
 * @since 1.8.0
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

    /**
     * IdiormPatch constructor.
     * @param object $ormObject
     */
    private function __construct()
    {
        //
    }

    /**
     * Get Instance
     *
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
     *
     * @param $ormObject
     * @return $this
     */
    public function setOrmObject($ormObject)
    {
        $this->ormObject = $ormObject;
        return $this;
    }

    /**
     * Add an LEFT JOIN source to the query
     *
     * @param string $table
     * @param array $constraint
     * @param string $table_alias
     * @return object
     */
    public function left_join($table, $constraint, $table_alias = null)
    {
        return $this->addJoin("LEFT", $table, $constraint, $table_alias);
    }

    /**
     * Add an RIGHT JOIN source to the query
     *
     * @param string $table
     * @param array $constraint
     * @param string $table_alias
     * @return object
     */
    public function right_join($table, $constraint, $table_alias = null)
    {
        return $this->addJoin("RIGHT", $table, $constraint, $table_alias);
    }

    /**
     *  Add Join 
     * 
     * @param string $operator
     * @param string $table
     * @param array $constraint
     * @param string $table_alias
     * @return object
     */
    public function addJoin($operator, $table, $constraint, $table_alias)
    {
        return $this->ormObject->_add_join_source($operator, $table, $constraint, $table_alias);
    }

}
