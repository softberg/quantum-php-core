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

namespace Quantum\Http\Traits\Request;

/**
 * Trait Query
 * @package Quantum\Http\Request
 */
trait Query
{
    /**
     * Query string
     */
    private static ?string $__query = null;

    /**
     * Gets the query string
     */
    public static function getQuery(): ?string
    {
        return self::$__query;
    }

    /**
     * Sets the query string
     */
    public static function setQuery(string $query): void
    {
        self::$__query = $query;
    }

    /**
     * Gets the query param
     */
    public static function getQueryParam(string $key): ?string
    {
        if (self::$__query === null) {
            return null;
        }

        $query = explode('&', self::$__query);

        foreach ($query as $items) {
            $item = explode('=', $items);
            if ($item[0] == $key) {
                return $item[1];
            }
        }

        return null;
    }

    /**
     * Sets the query param
     */
    public static function setQueryParam(string $key, string $value): void
    {
        $queryParams = self::$__query ? explode('&', self::$__query) : [];
        $queryParams[] = $key . '=' . $value;
        self::$__query = implode('&', $queryParams);
    }
}
