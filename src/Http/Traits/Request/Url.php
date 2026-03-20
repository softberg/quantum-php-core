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

namespace Quantum\Http\Traits\Request;

/**
 * Trait Url
 * @package Quantum\Http\Request
 */
trait Url
{
    /**
     * Scheme
     */
    private static ?string $__protocol = null;

    /**
     * Host name
     */
    private static ?string $__host = null;

    /**
     * Server port
     */
    private static ?string $__port = null;

    /**
     * Request URI
     */
    private static ?string $__uri = null;

    /**
     * Gets the protocol
     * @return string
     */
    public static function getProtocol(): ?string
    {
        return self::$__protocol;
    }

    /**
     * Sets the protocol
     */
    public static function setProtocol(string $protocol): void
    {
        self::$__protocol = $protocol;
    }

    /**
     * Gets the host name
     * @return string
     */
    public static function getHost(): ?string
    {
        return self::$__host;
    }

    /**
     * Sets the host name
     */
    public static function setHost(string $host): void
    {
        self::$__host = $host;
    }

    /**
     * Gets the port
     * @return string
     */
    public static function getPort(): ?string
    {
        return self::$__port;
    }

    /**
     * Sets the port
     */
    public static function setPort(string $port): void
    {
        self::$__port = $port;
    }

    /**
     * Gets the URI
     */
    public static function getUri(): ?string
    {
        return self::$__uri;
    }

    /**
     * Sets the URI
     */
    public static function setUri(string $uri): void
    {
        self::$__uri = ltrim($uri, '/');
    }

    /**
     * Returns the URI segment at the specified index.
     */
    public static function getSegment(int $index): ?string
    {
        $segments = self::getAllSegments();

        return $segments[$index] ?? null;
    }

    /**
     * Gets all URI segments as an array.
     * @return array<string>
     */
    public static function getAllSegments(): array
    {
        if (self::$__uri === null) {
            return ['zero_segment'];
        }

        $segments = explode('/', trim(parse_url(self::$__uri)['path'], '/'));
        array_unshift($segments, 'zero_segment');
        return $segments;
    }
}
