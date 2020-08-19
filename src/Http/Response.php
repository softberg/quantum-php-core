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

/**
 * Class Response
 * @package Quantum\Http
 * @method static void send()
 * @method static void redirect($url, $code = null)
 */
class Response
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
        return HttpResponse::$function(...$arguments);
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
        return HttpResponse::$function(...$arguments);
    }

}
