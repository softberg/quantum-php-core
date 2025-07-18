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
 * @since 2.9.8
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
     * @var string
     */
    private static $__query = null;

    /**
     * Gets the query string
     * @return string|null
     */
    public static function getQuery(): ?string
    {
        return self::$__query;
    }

    /**
     * Sets the query string
     * @param string $query
     */
    public static function setQuery(string $query)
    {
        self::$__query = $query;
    }

    /**
     * Gets the query param
     * @param string $key
     * @return string|null
     */
    public static function getQueryParam(string $key): ?string
    {
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
     * @param string $key
     * @param string $value
     */
    public static function setQueryParam(string $key, string $value)
    {
        $queryParams = self::$__query ? explode('&', self::$__query) : [];
        $queryParams[] = $key . '=' . $value;
        self::$__query = implode('&', $queryParams);
    }
}