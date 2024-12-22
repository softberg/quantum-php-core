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
 * @since 2.9.5
 */

namespace Quantum\Http\Request;

use Quantum\Exceptions\HttpException;
use Quantum\Exceptions\DiException;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Environment\Server;
use ReflectionException;

/**
 * Class HttpRequest
 * @package Quantum\Http
 */
abstract class HttpRequest
{

    use Header;
    use Body;
    use Url;
    use Query;
    use Params;
    use File;

    /**
     * Multipart form data
     */
    const CONTENT_FORM_DATA = 'multipart/form-data';

    /**
     * JSON payload
     */
    const CONTENT_JSON_PAYLOAD = 'application/json';

    /**
     * URL encoded
     */
    const CONTENT_URL_ENCODED = 'application/x-www-form-urlencoded';

    /**
     * Available methods
     */
    const METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Request method
     * @var string
     */
    private static $__method = null;

    /**
     * @var bool
     */
    private static $initialized = false;

    /**
     * Server
     * @var Server
     */
    private static $server;

    /**
     * Initiates the Request
     * @param Server $server
     * @throws DiException
     * @throws HttpException
     * @throws ReflectionException
     */
    public static function init(Server $server)
    {
        if (self::$initialized) {
            return;
        }

        self::$server = $server;

        self::$__method = self::$server->method();

        self::$__protocol = self::$server->protocol();

        self::$__host = self::$server->host();

        self::$__port = self::$server->port();

        self::$__uri = self::$server->uri();

        self::$__query = self::$server->query();

        self::$__headers = array_change_key_case((array)getallheaders(), CASE_LOWER);

        list('params' => $params, 'files' => $files) = self::parsedParams();

        self::$__request = array_merge(
            self::$__request,
            self::getParams(),
            self::postParams(),
            $params
        );

        self::$__files = array_merge(
            self::handleFiles($_FILES),
            $files
        );

        self::$initialized = true;
    }

    /**
     * Creates new request for internal use
     * @param string $method
     * @param string $url
     * @param array|null $data
     * @param array|null $files
     * @throws ReflectionException
     * @throws HttpException
     * @throws DiException
     */
    public static function create(string $method, string $url, array $data = null, array $files = null)
    {
        $parsed = parse_url($url);

        self::setMethod($method);

        if (isset($parsed['scheme'])) {
            self::setProtocol($parsed['scheme']);
        }

        if (isset($parsed['host'])) {
            self::setHost($parsed['host']);
        }

        if (isset($parsed['port'])) {
            self::setPort($parsed['port']);
        }
        if (isset($parsed['path'])) {
            self::setUri($parsed['path']);
        }
        if (isset($parsed['query'])) {
            self::setQuery($parsed['query']);
        }

        if ($data) {
            self::$__request = $data;
        }

        if ($files) {
            self::$__files = self::handleFiles($files);
        }
    }

    /**
     * Flushes the request header , body and files
     */
    public static function flush()
    {
        self::$__headers = [];
        self::$__request = [];
        self::$__files = [];
        self::$__protocol = null;
        self::$__host = null;
        self::$__port = null;
        self::$__uri = null;
        self::$__query = null;
    }

    /**
     * Gets the request method
     * @return string|null
     */
    public static function getMethod(): ?string
    {
        return self::$__method;
    }

    /**
     * Sets the request method
     * @param string $method
     * @throws HttpException
     */
    public static function setMethod(string $method)
    {
        if (!in_array($method, self::METHODS)) {
            throw HttpException::methodNotAvailable($method);
        }

        self::$__method = $method;
    }

    /**
     * @param string $method
     * @return bool
     */
    public static function isMethod(string $method): bool
    {
        return strcasecmp($method, self::$__method) == 0;
    }

    /**
     * Gets the nth segment
     * @param integer $number
     * @return string|null
     */
    public static function getSegment(int $number): ?string
    {
        $segments = self::getAllSegments();

        if (isset($segments[$number])) {
            return $segments[$number];
        }

        return null;
    }

    /**
     * Gets the segments of current URI
     * @return array
     */
    public static function getAllSegments(): array
    {
        $segments = explode('/', trim(parse_url(self::$__uri)['path'], '/'));
        array_unshift($segments, 'zero_segment');
        return $segments;
    }

    /**
     * Gets Cross Site Request Forgery Token
     * @return string|null
     */
    public static function getCsrfToken(): ?string
    {
        $csrfToken = null;

        if (self::has(Csrf::TOKEN_KEY)) {
            $csrfToken = (string)self::get(Csrf::TOKEN_KEY);
        } elseif (self::hasHeader('X-' . Csrf::TOKEN_KEY)) {
            $csrfToken = self::getHeader('X-' . Csrf::TOKEN_KEY);
        }

        return $csrfToken;
    }

    /**
     * Gets Authorization Bearer token
     * @return string|null
     */
    public static function getAuthorizationBearer(): ?string
    {
        $bearerToken = null;

        $authorization = (string)self::getHeader('Authorization');

        if (self::hasHeader('Authorization')) {
            if (preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
                $bearerToken = $matches[1];
            }
        }

        return $bearerToken;
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

}
