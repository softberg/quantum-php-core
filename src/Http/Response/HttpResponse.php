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

namespace Quantum\Http\Response;

use Quantum\Http\Traits\Response\Header;
use Quantum\Http\Traits\Response\Status;
use Quantum\Http\Traits\Response\Body;
use Quantum\Environment\Environment;
use Quantum\Environment\Enums\Env;
use Quantum\Http\Enums\StatusCode;
use Exception;

/**
 * Class HttpResponse
 * @package Quantum\Http\Response
 */
abstract class HttpResponse
{

    use Header;
    use Body;
    use Status;

    /**
     * XML root element
     * @var string
     */
    private static $xmlRoot = '<data></data>';

    /**
     * Callback function
     * @var string
     */
    private static $callbackFunction = '';

    /**
     * @var bool
     */
    private static $initialized = false;

    /**
     * Initialize the Response
     */
    public static function init()
    {
        if (self::$initialized) {
            return;
        }

        self::flush();

        self::$initialized = true;
    }

    /**
     * Flushes the response header and body
     */
    public static function flush()
    {
        self::$__statusCode = StatusCode::OK;
        self::$__headers = [];
        self::$__response = [];
        self::$xmlRoot = '<data></data>';
        self::$callbackFunction = '';
        self::$initialized = false;
    }

    /**
     * Sends all response data to the client and finishes the request.
     * @throws Exception
     */
    public static function send()
    {
        if (Environment::getInstance()->getAppEnv() !== Env::TESTING) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
        }

        foreach (self::$__headers as $key => $value) {
            header($key . ': ' . $value);
        }

        http_response_code(self::getStatusCode());

        echo self::getContent();
    }
}