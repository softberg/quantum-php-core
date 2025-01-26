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
 * @since 2.9.0
 */

namespace Quantum\Http;

use Quantum\Http\Response\HttpResponse;

/**
 * Class Response
 * @package Quantum\Http
 * @method static void init()
 * @method static void flush()
 * @method static void send()
 * @method static string getContent()
 * @method static void setStatusCode(int $code)
 * @method static int getStatusCode()
 * @method static string getStatusText()
 * @method static void redirect(string $url, int $code = null)
 * @method static void json(array $data = null, int $code = null)
 * @method static void xml(array $data = null, $root = '<data></data>', int $code = null)
 * @method static void html(string $html, int $code = null)
 * @method static bool has(string $key)
 * @method static mixed get(string $key, string $default = null)
 * @method static void set(string $key, $value)
 * @method static array all()
 * @method static void delete(string $key)
 * @method static bool hasHeader(string $key))
 * @method static string getHeader(string $key)
 * @method static void setHeader(string $key, string $value)
 * @method static array allHeaders()
 * @method static void deleteHeader(string $key)
 * @method static void setContentType(string $contentType)
 * @method static string|null getContentType()
 * @mixin HttpResponse
 */
class Response
{

    /**
     * @param string $function The function name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $function, array $arguments)
    {
        return HttpResponse::$function(...$arguments);
    }

    /**
     * @param string $function The function name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $function, array $arguments)
    {
        return HttpResponse::$function(...$arguments);
    }
}
