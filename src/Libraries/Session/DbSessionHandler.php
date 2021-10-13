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

namespace Quantum\Libraries\Session;

use Quantum\Libraries\Database\DbalInterface;
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
     * DbSessionHandler constructor.
     * @param DbalInterface $orm
     */

    /**
     * DbSessionHandler constructor.
     * @param \Quantum\Libraries\Database\DbalInterface $orm
     */
    public function __construct(DbalInterface $orm)
    {
        $this->orm = $orm;
    }

    /**
     * @inheritDoc
     */
    public function open($path, $name): bool
    {
        if ($this->orm::getConnection()) {
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function close(): bool
    {
        if (!$this->orm::getConnection()) {
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function read($id): string
    {
        $result = $this->orm->query('SELECT * FROM ' . $this->orm->getTable() . " WHERE id = :id", ['id' => $id]);
        return $result ? $result->data : '';
    }

    /**
     * @inheritDoc
     */
    public function write($id, $data): bool
    {
        $access = time();
        return $this->orm->execute("REPLACE INTO " . $this->orm->getTable() . " VALUES (:id, :access, :data)", ['id' => $id, 'access' => $access, 'data' => $data]);
    }

    /**
     * @inheritDoc
     */
    public function destroy($id): bool
    {
        return $this->orm->execute("DELETE FROM " . $this->orm->getTable() . " WHERE id = :id", ['id' => $id]);
    }

    /**
     * @inheritDoc
     */
    public function gc($max_lifetime): bool
    {
        $old = time() - $max_lifetime;
        return $this->orm->execute("DELETE * FROM " . $this->orm->getTable() . " WHERE access < :old", ['old' => $old]);
    }

}
