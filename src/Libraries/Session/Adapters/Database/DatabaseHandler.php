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
 * @since 3.0.0
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
        return (bool) $this->sessionModel::getConnection();
    }

    /**
     * @inheritDoc
     */
    public function close(): bool
    {
        return !$this->sessionModel::getConnection();
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
    public function gc($max_lifetime)
    {
        $old = time() - $max_lifetime;

        $result = $this->sessionModel->criteria('ttl', '<', $old)->deleteMany();

        if ($result === false) {
            return false;
        }

        return 0;
    }
}
