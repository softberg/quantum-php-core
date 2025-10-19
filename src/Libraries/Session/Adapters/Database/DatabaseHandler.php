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

namespace Quantum\Libraries\Session\Adapters\Database;

use Quantum\Libraries\Database\Contracts\DbalInterface;
use SessionHandlerInterface;

/**
 * Class DatabaseHandler
 * @package Quantum\Libraries\Session
 */
class DatabaseHandler implements SessionHandlerInterface
{

    /**
     * @var DbalInterface
     */
    private $sessionModel;

    /**
     * DatabaseHandler constructor.
     * @param DbalInterface $sessionModel
     */
    public function __construct(DbalInterface $sessionModel)
    {
        $this->sessionModel = $sessionModel;
    }

    /**
     * @inheritDoc
     */
    public function open($path, $name): bool
    {
        if ($this->sessionModel::getConnection()) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function close(): bool
    {
        if (!$this->sessionModel::getConnection()) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function read($id): string
    {
        $result = $this->sessionModel->findOneby('session_id', $id);
        return $result->prop('data') ?: '';
    }

    /**
     * @inheritDoc
     */
    public function write($id, $data): bool
    {
        $sessionItem = $this->sessionModel->findOneBy('session_id', $id);

        if (empty($sessionItem->asArray())) {
            $sessionItem = $this->sessionModel->create();
        }

        $sessionItem->prop('session_id', $id);
        $sessionItem->prop('ttl', time());
        $sessionItem->prop('data', $data);

        return $sessionItem->save();
    }

    /**
     * @inheritDoc
     */
    public function destroy($id): bool
    {
        if ($this->sessionModel->findOneBy('session_id', $id)->asArray()) {
            return $this->sessionModel->findOneBy('session_id', $id)->delete();
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function gc($max_lifetime): int|false
    {
        $old = time() - $max_lifetime;
        $deleted = $this->sessionModel->criteria('ttl', '<', $old)->deleteMany();
        return $deleted !== false ? $deleted : false;
    }
}