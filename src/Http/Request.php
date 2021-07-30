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
 * @since 2.4.0
 */

namespace Quantum\Http;

use Quantum\Http\Request\HttpRequest;

/**
 * Class Request
 * @package Quantum\Http
 *
 * @method static void init(Server $server)
 * @method static void create(string $method, string $url, array $data = null, array $file = null)
 * @method static void flush()
 * @method static string|null getMethod()
 * @method static void setMethod(string $method)
 * @method static bool isMethod()
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
 * @method static void all()
 * @method static array delete(string $key)
 * @method static bool hasFile(string $key)
 * @method static \Quantum\Libraries\Upload\File getFile(string $key)
 * @method static bool hasHeader(string $key)
 * @method static mixed|null getHeader(string $key)
 * @method static void setHeader(string $key, $value)
 * @method static array allHeaders()
 * @method static void deleteHeader(string $key)
 * @method static string|null getSegment(int $number)
 * @method static array getAllSegments()
 * @method static string|null getCSRFToken()
 * @method static string|null getAuthorizationBearer()
 * @method static bool isAjax()
 * @method static string getReferrer()
 * @method static array|null getParams()
 * @method static array|null postParams()
 * @method static array getRawInputs()
 * @method static array handleFiles(array $_files)
 */
class Request
{

    /**
     * __call magic
     * @param string $function The function name
     * @param array $arguments 
     * @return mixed
     */
    public function __call(string $function, array $arguments)
    {
        return HttpRequest::$function(...$arguments);
    }

    /**
     * __callStatic magic
     * @param string $function The function name
     * @param array $arguments 
     * @return mixed
     */
    public static function __callStatic(string $function, array $arguments)
    {
        return HttpRequest::$function(...$arguments);
    }

}
