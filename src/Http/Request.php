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

namespace Quantum\Http;

use Quantum\Http\Request\HttpRequest;

/**
 * Class Request
 * @package Quantum\Http
 * @method static void create(string $method, string $url, array $data = null, array $file = null)
 * @method static void flush()
 * @method static string|null getMethod()
 * @method static void setMethod(string $method)
 * @method static bool isMethod(string $method)
 * @method static string getProtocol()
 * @method static void setProtocol($protocol)
 * @method static string getHost()
 * @method static void setHost($host)
 * @method static string getPort()
 * @method static void setPort($port)
 * @method static string|null getUri()
 * @method static void setUri($uri)
 * @method static string getQuery()
 * @method static string|null getQueryParam(string $key)
 * @method static void setQueryParam(string $key, string $value)
 * @method static void setQuery($query)
 * @method static bool has(string $key)
 * @method static mixed get(string $key, string $default = null, bool $raw = false)
 * @method static void set(string $key, $value)
 * @method static array all()
 * @method static void delete(string $key)
 * @method static bool hasFile(string $key)
 * @method static mixed getFile(string $key)
 * @method static bool hasHeader(string $key)
 * @method static string|null getHeader(string $key)
 * @method static void setHeader(string $key, $value)
 * @method static array allHeaders()
 * @method static void deleteHeader(string $key)
 * @method static string|null getSegment(int $number)
 * @method static array getAllSegments()
 * @method static string|null getCsrfToken()
 * @method static string|null getAuthorizationBearer()
 * @method static array|null getBasicAuthCredentials()
 * @method static bool isAjax()
 * @method static string|null getReferrer()
 * @mixin HttpRequest
 */
class Request
{

    /**
     * @param string $function The function name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $function, array $arguments)
    {
        return HttpRequest::$function(...$arguments);
    }

    /**
     * @param string $function The function name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $function, array $arguments)
    {
        return HttpRequest::$function(...$arguments);
    }
}