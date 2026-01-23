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
 * @since 3.0.0
 */

namespace Quantum\Http\Traits\Request;

/**
 * Trait Header
 * @package Quantum\Http\Request
 */
trait Header
{
    /**
     * Request headers
     * @var array
     */
    private static $__headers = [];

    /**
     * Checks the request header existence by given key
     * @param string $key
     * @return bool
     */
    public static function hasHeader(string $key): bool
    {
        [$keyWithHyphens, $keyWithUnderscores] = self::normalizeHeaderKey($key);

        return isset(self::$__headers[$keyWithHyphens]) || isset(self::$__headers[$keyWithUnderscores]);
    }

    /**
     * Gets the request header by given key
     * @param string $key
     * @return string|null
     */
    public static function getHeader(string $key): ?string
    {
        if (self::hasHeader($key)) {
            [$keyWithHyphens, $keyWithUnderscores] = self::normalizeHeaderKey($key);
            return self::$__headers[$keyWithHyphens] ?? self::$__headers[$keyWithUnderscores];
        }

        return null;
    }

    /**
     * Sets the request header
     * @param string $key
     * @param mixed $value
     */
    public static function setHeader(string $key, $value)
    {
        self::$__headers[strtolower($key)] = $value;
    }

    /**
     * Gets all request headers
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
            unset(self::$__headers[strtolower($key)]);
        }
    }

    /**
     * Gets Authorization Bearer token
     * @return string|null
     */
    public static function getAuthorizationBearer(): ?string
    {
        $bearerToken = null;

        $authorization = (string) self::getHeader('Authorization');

        if (self::hasHeader('Authorization') && preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
            $bearerToken = $matches[1];
        }

        return $bearerToken;
    }

    /**
     * Gets Basic Auth Credentials
     * @return array|null
     */
    public static function getBasicAuthCredentials(): ?array
    {
        if (self::$server->has('PHP_AUTH_USER') && static::$server->has('PHP_AUTH_PW')) {
            return [
                'username' => self::$server->get('PHP_AUTH_USER'),
                'password' => self::$server->get('PHP_AUTH_PW'),
            ];
        }

        if (!self::hasHeader('Authorization')) {
            return null;
        }

        $authorization = (string) self::getHeader('Authorization');

        if (preg_match('/Basic\s(\S+)/', $authorization, $matches)) {
            $decoded = base64_decode($matches[1], true);

            if ($decoded && strpos($decoded, ':') !== false) {
                [$username, $password] = explode(':', $decoded, 2);
                return ['username' => $username, 'password' => $password];
            }
        }

        return null;
    }

    /**
     * Checks to see if request was AJAX request
     * @return bool
     */
    public static function isAjax(): bool
    {
        return self::hasHeader('X-REQUESTED-WITH') || self::$server->ajax();
    }

    /**
     * Gets the referrer
     * @return string|null
     */
    public static function getReferrer(): ?string
    {
        return self::$server->referrer();
    }

    /**
     * @param string $key
     * @return array
     */
    private static function normalizeHeaderKey(string $key): array
    {
        $keyWithHyphens = str_replace('_', '-', strtolower($key));
        $keyWithUnderscores = str_replace('-', '_', $key);

        return [$keyWithHyphens, $keyWithUnderscores];
    }
}
