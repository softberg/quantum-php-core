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
 * @since 2.5.0
 */

namespace Quantum\Http;

use Quantum\Http\Response\HttpResponse;

/**
 * Class Response
 * @package Quantum\Http
 * @mixin HttpResponse
 */
class Response
{

    /**
     * __call magic
     * @param string $function The function name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $function, array $arguments)
    {
        return HttpResponse::$function(...$arguments);
    }

    /**
     * __callStatic magic
     * @param string $function The function name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $function, array $arguments)
    {
        return HttpResponse::$function(...$arguments);
    }

}
