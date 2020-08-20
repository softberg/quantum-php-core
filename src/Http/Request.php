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
 * @since 2.0.0
 */

namespace Quantum\Http;

use Quantum\Http\HttpRequest;

/**
 * Class Request
 * @package Quantum\Http
 * @method static string|null getSegment($number)
 * @method static string getPort()
 * @method static string getHost()
 * @method static string getProtocol()
 * @method static string getQuery()
 * @method static string getReferrer()
 * @method static string|null getUri()
 * @method static void setHeader($key, $value)
 * @method static mixed|null getHeader($key)
 * @method static bool hasHeader($key)
 * @method static void deleteHeader($key)
 * @method static string getAuthorizationBearer()
 * @method static string getCSRFToken()
 * @method static mixed getMethod()
 */
class Request
{

    /**
     * __call magic
     * 
     * @param string $function The function name
     * @param array $arguments 
     * @return mixed
     */
    public function __call($function, $arguments)
    {
        return HttpRequest::$function(...$arguments);
    }

    /**
     * __callStatic magic
     * 
     * @param string $function The function name
     * @param array $arguments 
     * @return mixed
     */
    public static function __callStatic($function, $arguments)
    {
        return HttpRequest::$function(...$arguments);
    }

}
