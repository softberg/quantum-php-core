<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
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
     */
    private static array $__headers = [];

    /**
     * Checks the response header existence by given key
     */
    public static function hasHeader(string $key): bool
    {
        return isset(self::$__headers[$key]);
    }

    /**
     * Gets the response header by given key
     */
    public static function getHeader(string $key): ?string
    {
        return self::hasHeader($key) ? self::$__headers[$key] : null;
    }

    /**
     * Sets the response header
     */
    public static function setHeader(string $key, string $value): void
    {
        self::$__headers[$key] = $value;
    }

    /**
     * Get all response headers
     */
    public static function allHeaders(): array
    {
        return self::$__headers;
    }

    /**
     * Deletes the header by given key
     */
    public static function deleteHeader(string $key): void
    {
        if (self::hasHeader($key)) {
            unset(self::$__headers[$key]);
        }
    }

    /**
     * Sets the content type
     */
    public static function setContentType(string $contentType): void
    {
        self::setHeader('Content-Type', $contentType);
    }

    /**
     * Gets the content type
     */
    public static function getContentType(): string
    {
        return self::getHeader('Content-Type') ?? ContentType::HTML;
    }

    /**
     * Redirect
     * @throws StopExecutionException
     */
    public static function redirect(string $url, int $code = 302): void
    {
        self::setStatusCode($code);
        self::setHeader('Location', $url);
        stop();
    }
}
