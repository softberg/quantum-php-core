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

namespace Quantum\Http\Request;

use Quantum\Config\Exceptions\ConfigException;
use Quantum\Http\Exceptions\HttpException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Http\Traits\Request\RawInput;
use Quantum\Http\Traits\Request\Internal;
use Quantum\Http\Traits\Request\Header;
use Quantum\Http\Traits\Request\Params;
use Quantum\Di\Exceptions\DiException;
use Quantum\Http\Traits\Request\Query;
use Quantum\Http\Traits\Request\Body;
use Quantum\Http\Traits\Request\File;
use Quantum\Http\Traits\Request\Url;
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
    use RawInput;
    use Internal;

    /**
     * Available methods
     */
    const METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Default port for HTTP
     */
    const DEFAULT_HTTP_PORT = 80;

    /**
     * Default port for HTTPS
     */
    const DEFAULT_HTTPS_PORT = 443;

    /**
     * Request method
     * @var string
     */
    private static $__method = null;

    /**
     * @var Server
     */
    protected static $server;

    /**
     * @var bool
     */
    private static $initialized = false;

    /**
     * Initializes the request static properties using the server instance.
     * @param Server $server
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public static function init(Server $server)
    {
        if (self::$initialized) {
            return;
        }

        self::flush();

        self::$server = $server;

        self::setServerInfo();
        self::setContentType();
        self::setRequestHeaders();

        list('params' => $rawInputParams, 'files' => $rawInputFiles) = self::getRawInputParams();

        self::setRequestParams($rawInputParams);
        self::setUploadedFiles($rawInputFiles);

        self::$initialized = true;
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

        self::$initialized = false;
    }

    /**
     * Sets the merged request parameters
     * @param array $params
     */
    public static function setRequestParams(array $params)
    {
        self::$__request = array_merge(
            self::getParams(),
            self::postParams(),
            self::jsonPayloadParams(),
            self::urlEncodedParams(),
            $params
        );
    }

    /**
     * Sets the uploaded files array merging handled $_FILES and parsed files
     * @param array $files
     * @throws BaseException
     * @throws ReflectionException
     */
    public static function setUploadedFiles(array $files)
    {
        self::$__files = array_merge(
            self::handleFiles($_FILES),
            $files
        );
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
     * @return void
     * @throws BaseException
     */
    public static function setMethod(string $method)
    {
        if (!in_array(strtoupper($method), self::METHODS)) {
            throw HttpException::requestMethodNotAvailable($method);
        }

        self::$__method = $method;
    }

    /**
     * Checks if the current method matches the given method
     * @param string $method
     * @return bool
     */
    public static function isMethod(string $method): bool
    {
        return strcasecmp($method, self::$__method) === 0;
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
     * Gets the base url
     * @param bool $withModulePrefix
     * @return string
     */
    public static function getBaseUrl(bool $withModulePrefix = false): string
    {
        $baseUrl = config()->get('app.base_url');

        $prefix = route_prefix();
        $modulePrefix = ($withModulePrefix && !in_array($prefix, [null, '', '0'], true)) ? '/' . $prefix : '';

        if ($baseUrl) {
            return $baseUrl . $modulePrefix;
        }

        return self::getHostPrefix() . $modulePrefix;
    }

    /**
     * Gets the current url
     * @return string
     */
    public static function getCurrentUrl(): string
    {
        $uri = self::getUri();
        $query = self::getQuery();
        $queryPart = $query ? '?' . $query : '';

        return self::getHostPrefix() . '/' . $uri . $queryPart;
    }

    /**
     * Gets the protocol, host, and optional port part of the URL.
     * @return string
     */
    private static function getHostPrefix(): string
    {
        $protocol = self::getProtocol();
        $host = self::getHost();
        $port = self::getPort();

        $defaultPort = $protocol === 'https' ? self::DEFAULT_HTTPS_PORT : self::DEFAULT_HTTP_PORT;

        $portPart = ($port && $port != $defaultPort) ? ':' . $port : '';

        return $protocol . '://' . $host . $portPart;
    }

    /**
     * Sets server data (method, protocol, host, port, uri, query).
     */
    private static function setServerInfo()
    {
        foreach (['method', 'protocol', 'host', 'port', 'uri', 'query'] as $name) {
            self::${"__{$name}"} = self::$server->$name();
        }
    }

    /**
     * Sets the normalized request content type.
     */
    private static function setContentType()
    {
        self::$__contentType = self::$server->contentType(true);
    }

    /**
     * Sets request headers, normalizing keys to lowercase.
     */
    private static function setRequestHeaders()
    {
        self::$__headers = array_change_key_case((array)getallheaders());
    }
}