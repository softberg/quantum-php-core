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

namespace Quantum\Http\Traits\Response;

use Quantum\App\Exceptions\StopExecutionException;
use Quantum\Http\Enums\ContentType;

/**
 * Trait Header
 * @package Quantum\Http\Response
 */
trait Header
{

    /**
     * Response headers
     * @var array
     */
    private static $__headers = [];

    /**
     * Checks the response header existence by given key
     * @param string $key
     * @return bool
     */
    public static function hasHeader(string $key): bool
    {
        return isset(self::$__headers[$key]);
    }

    /**
     * Gets the response header by given key
     * @param string $key
     * @return string|null
     */
    public static function getHeader(string $key): ?string
    {
        return self::hasHeader($key) ? self::$__headers[$key] : null;
    }

    /**
     * Sets the response header
     * @param string $key
     * @param string $value
     */
    public static function setHeader(string $key, string $value)
    {
        self::$__headers[$key] = $value;
    }

    /**
     * Get all response headers
     * @return array
     */
    public static function allHeaders(): array
    {
        return self::$__headers;
    }

    /**
     * Deletes the header by given key
     * @param string $key
     */
    public static function deleteHeader(string $key)
    {
        if (self::hasHeader($key)) {
            unset(self::$__headers[$key]);
        }
    }

    /**
     * Sets the content type
     * @param string $contentType
     */
    public static function setContentType(string $contentType)
    {
        self::setHeader('Content-Type', $contentType);
    }

    /**
     * Gets the content type
     * @return string|null
     */
    public static function getContentType(): string
    {
        return self::getHeader('Content-Type') ?? ContentType::HTML;
    }

    /**
     * Redirect
     * @param string $url
     * @param int $code
     * @throws StopExecutionException
     */
    public static function redirect(string $url, int $code = 302)
    {
        self::setStatusCode($code);
        self::setHeader('Location', $url);
        stop();
    }
}