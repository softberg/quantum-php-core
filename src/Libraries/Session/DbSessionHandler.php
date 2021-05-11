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
 * @since 2.0.0
 */

namespace Quantum\Libraries\Session;

use Quantum\Libraries\Database\DbalInterface;
use Quantum\Libraries\Database\Database;
use Quantum\Loader\Loader;
use \SessionHandlerInterface;

/**
 * Class DbSessionHandler
 * @package Quantum\Libraries\Session
 */
class DbSessionHandler implements SessionHandlerInterface
{

    /**
     * The ORM instance
     * @var \Quantum\Libraries\Database\DbalInterface
     */
    private $orm;

    /**
     * Loader instance
     * @var \Quantum\Loader\Loader
     */
    private $loader;

    /**
     * DbSessionHandler constructor.
     * @param DbalInterface $orm
     * @param Loader $loader
     */

    /**
     * DbSessionHandler constructor.
     * @param \Quantum\Libraries\Database\DbalInterface $orm
     * @param \Quantum\Loader\Loader $loader
     */
    public function __construct(DbalInterface $orm, Loader $loader)
    {
        $this->orm = $orm;
        $this->loader = $loader;
    }

    /**
     * Initialize session
     * @param string $save_path
     * @param string $name
     * @return bool
     */
    public function open($save_path, $name)
    {
        if ((new Database($this->loаder))->connected()) {
            return true;
        }
        return false;
    }

    /**
     * Close the session
     * @return bool
     */
    public function close()
    {
        if (!(new Database($this->loаder))->connected()) {
            return true;
        }
        return false;
    }

    /**
     * Read session data
     * @param string $id The session id
     * @return string
     */
    public function read($id)
    {
        $result = $this->orm->query('SELECT * FROM ' . $this->orm->getTable() . " WHERE id = :id", ['id' => $id]);
        return $result ? $result->data : '';
    }

    /**
     * Write session data
     * @param string $id The session id
     * @param mixed $data
     * @return bool
     */
    public function write($id, $data)
    {
        $access = time();
        return $this->orm->execute("REPLACE INTO " . $this->orm->getTable() . " VALUES (:id, :access, :data)", ['id' => $id, 'access' => $access, 'data' => $data]);
    }

    /**
     * Destroy a session
     * @param int $id The session ID
     * @return bool
     */
    public function destroy($id)
    {
        return $this->orm->execute("DELETE FROM " . $this->orm->getTable() . " WHERE id = :id", ['id' => $id]);
    }

    /**
     * Cleanup old sessions
     * @param int $max Max lifetime
     * @return bool
     */
    public function gc($max)
    {
        $old = time() - $max;
        return $this->orm->execute("DELETE * FROM " . $this->orm->getTable() . " WHERE access < :old", ['old' => $old]);
    }

}
