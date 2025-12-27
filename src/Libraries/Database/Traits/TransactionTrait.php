<?php

namespace Quantum\Libraries\Database\Traits;

use Quantum\Libraries\Database\Exceptions\DatabaseException;
use Quantum\App\Exceptions\BaseException;
use Throwable;

/**
 * Trait TransactionTrait
 * @package Quantum\Libraries\Database
 */
trait TransactionTrait
{

    /**
     * Begins a transaction
     * @throws BaseException
     */
    public static function beginTransaction(): void
    {
        self::resolveTransaction(__FUNCTION__);
    }

    /**
     * Commits a transaction
     * @throws BaseException
     * @throws DatabaseException
     */
    public static function commit(): void
    {
        self::resolveTransaction(__FUNCTION__);
    }

    /**
     * Rolls back a transaction
     * @throws BaseException
     */
    public static function rollback(): void
    {
        self::resolveTransaction(__FUNCTION__);
    }

    /**
     * Resolves the transaction method call
     * @param string $method
     * @return mixed
     * @throws BaseException
     */
    protected static function resolveTransaction(string $method)
    {
        $db = self::getInstance()->getOrmClass();

        if (!method_exists($db, $method)) {
            throw DatabaseException::methodNotSupported($method, self::class);
        }

        return $db::$method();
    }

    /**
     * Transaction wrapper using a closure
     * @param callable $callback
     * @return mixed
     * @throws BaseException
     * @throws DatabaseException
     * @throws Throwable
     */
    public static function transaction(callable $callback)
    {
        self::beginTransaction();

        try {
            $result = $callback();
            self::commit();
            return $result;
        } catch (Throwable $e) {
            self::rollback();
            throw $e;
        }
    }
}