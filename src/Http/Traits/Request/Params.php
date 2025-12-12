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
 * @since 2.9.9
 */

namespace Quantum\Http\Traits\Request;


use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Http\Enums\ContentType;
use ReflectionException;

/**
 * Trait Params
 * @package Quantum\Http\Request
 */
trait Params
{

    /**
     * Request content type
     * @var string|null
     */
    private static $__contentType = null;

    /**
     * Gets the GET params.
     * @return array
     */
    private static function getParams(): array
    {
        if (empty($_GET)) {
            return [];
        }

        return filter_input_array(INPUT_GET) ?: [];
    }

    /**
     * Gets the POST params.
     * @return array
     */
    private static function postParams(): array
    {
        if (empty($_POST)) {
            return [];
        }

        return filter_input_array(INPUT_POST) ?? [];
    }

    /**
     * Parses and returns JSON payload parameters.
     * @return array
     */
    private static function jsonPayloadParams(): array
    {
        if (
            !in_array(self::$__method, ['PUT', 'PATCH', 'POST'], true) ||
            self::$__contentType !== ContentType::JSON
        ) {
            return [];
        }

        return json_decode(self::getRawInput(), true) ?: [];
    }

    /**
     * Parses and returns URL-encoded parameters.
     * @return array
     */
    private static function urlEncodedParams(): array
    {
        if (
            !in_array(self::$__method, ['PUT', 'PATCH', 'POST'], true) ||
            self::$__contentType !== ContentType::URL_ENCODED
        ) {
            return [];
        }

        parse_str(urldecode(self::getRawInput()), $result);

        return $result;
    }

    /**
     * Parses and returns multipart form data parameters.
     * @return array[]
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    private static function getRawInputParams(): array
    {
        if (
            !in_array(self::$__method, ['PUT', 'PATCH', 'POST'], true) ||
            self::$__contentType !== ContentType::FORM_DATA
        ) {
            return ['params' => [], 'files' => []];
        }

        return self::parse(self::getRawInput());
    }

    /**
     * Retrieves the raw HTTP request body as a string.
     * @return string
     */
    private static function getRawInput(): string
    {
        return file_get_contents('php://input');
    }
}