<?php

declare(strict_types=1);

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

namespace Quantum\Database\Traits;

use Quantum\Database\Exceptions\DatabaseException;

/**
 * Trait RelationalTrait
 * @package Quantum\Database
 */
trait RelationalTrait
{
    /**
     * Raw execute
     * @throws DatabaseException
     */
    public static function execute(string $query, array $parameters = []): bool
    {
        return self::resolveQuery(__FUNCTION__, $query, $parameters);
    }

    /**
     * Raw query
     * @throws DatabaseException
     */
    public static function query(string $query, array $parameters = []): array
    {
        return self::resolveQuery(__FUNCTION__, $query, $parameters);
    }

    /**
     * Fetches table columns
     * @throws DatabaseException
     */
    public static function fetchColumns(string $query, array $parameters = []): array
    {
        return self::resolveQuery(__FUNCTION__, $query, $parameters);
    }

    /**
     * Gets the last query executed
     * @throws DatabaseException
     */
    public static function lastQuery(): ?string
    {
        return self::resolveQuery(__FUNCTION__);
    }

    /**
     * Get an array containing all the queries
     * run on a specified connection up to now.
     * @throws DatabaseException
     */
    public static function queryLog(): array
    {
        return self::resolveQuery(__FUNCTION__);
    }

    /**
     * Resolves the requested query
     * @return mixed
     * @throws DatabaseException
     */
    protected static function resolveQuery(string $method, string $query = '', array $parameters = [])
    {
        return self::getInstance()->getOrmClass()::$method($query, $parameters);
    }
}
