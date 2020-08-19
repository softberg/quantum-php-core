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
 * @method string|null getSegment($number)
 * @method string getPort()
 * @method string getHost()
 * @method string getProtocol()
 * @method string getQuery()
 * @method string getReferrer()
 * @method string|null getUri()
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
