<?php


namespace Quantum\Http\Request;


trait Query
{
    /**
     * Query string
     * @var string
     */
    private static $__query = null;

    /**
     * Gets the query string
     * @return string
     */
    public static function getQuery()
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
    public static function getQueryParam(string $key)
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
        array_push($queryParams, $key . '=' . $value);
        self::$__query = implode('&', $queryParams);
    }
}