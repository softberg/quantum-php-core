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
 * @since 1.0.0
 */

namespace Quantum\Libraries\Session;

use Quantum\Libraries\Database\Database;

/**
 * DB Session handler class
 *
 * @package Quantum
 * @subpackage Libraries.Session
 * @category Libraries
 */
class DbSessionHandler
{

    /**
     * Initialize session
     */
    public function _open()
    {
        if (Database::connected()) {
            return true;
        }
        return false;
    }

    /**
     * Close the session
     */
    public function _close()
    {
        if (!Database::connected()) {
            return true;
        }
        return false;
    }

    /**
     * Read session data
     *
     * @param string $id The session id
     * @return string
     */
    public function _read($id)
    {
        $result = $this->orm->query('SELECT * FROM ' . $this->table . " WHERE id = :id", ['id' => $id], false);
        return $result ? $result->data : '';
    }

    /**
     * Write session data
     *
     * @param string $id The session id
     * @param mixed $data
     * @return bool
     */
    public function _write($id, $data)
    {
        $access = time();
        return $this->orm->execute("REPLACE INTO " . $this->table . " VALUES (:id, :access, :data)", ['id' => $id, 'access' => $access, 'data' => $data]);
    }

    /**
     * Destroy a session
     *
     * @param type $id The session ID
     * @return bool
     */
    public function _destroy($id)
    {
        return $this->orm->execute("DELETE FROM " . $this->table . " WHERE id = :id", ['id' => $id]);
    }

    /**
     * Cleanup old sessions
     *
     * @param int $max Max lifetime
     */
    public function _gc($max)
    {
        $old = time() - $max;

        return $this->orm->execute("DELETE * FROM " . $this->table . " WHERE access < :old", ['old' => $old]);
    }
}
