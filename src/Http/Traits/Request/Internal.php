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

namespace Quantum\Http\Traits\Request;

use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Http\Constants\ContentType;
use Quantum\Di\Exceptions\DiException;
use Quantum\Environment\Server;
use ReflectionException;

/**
 * Trait Internal
 * @package Quantum\Http\Request
 */
trait Internal
{

    /**
     * Creates an internal request for testing purposes
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     * @param array $files
     * @throws BaseException
     * @throws ReflectionException
     * @throws ConfigException
     * @throws DiException
     */
    public static function create(
        string $method,
        string $url,
        array  $params = [],
        array  $headers = [],
        array  $files = []
    )
    {
        $parsed = parse_url($url);

        $server = Server::getInstance();

        $server->flush();

        $server->set('REQUEST_METHOD', strtoupper($method));

        $path = isset($parsed['path']) ? ltrim($parsed['path'], '/') : '/';

        $server->set('REQUEST_URI', $path);

        if (isset($parsed['scheme'])) {
            $server->set('REQUEST_SCHEME', $parsed['scheme']);
            $server->set('HTTPS', $parsed['scheme'] === 'https' ? 'on' : 'off');
        }

        if (isset($parsed['host'])) {
            $server->set('HTTP_HOST', $parsed['host']);
            $server->set('SERVER_NAME', $parsed['host']);
        }

        if (isset($parsed['port'])) {
            $server->set('SERVER_PORT', $parsed['port']);
        } else {
            $server->set('SERVER_PORT', ($parsed['scheme'] ?? '') === 'https' ? 443 : 80);
        }

        if (isset($parsed['query'])) {
            $server->set('QUERY_STRING', $parsed['query']);
        } else {
            $server->set('QUERY_STRING', '');
        }

        self::detectAndSetContentType($server, $params, $files);

        if ($headers) {
            foreach ($headers as $name => $value) {
                $server->set('HTTP_' . strtoupper($name), $value);
            }
        }

        self::flush();

        self::init($server);

        if ($params) {
            self::setRequestParams($params);
        }

        if ($files) {
            self::setUploadedFiles(self::handleFiles($files));
        }
    }

    /**
     * Detects the content type
     * @param $server
     * @param array|null $data
     * @param array|null $files
     * @return void
     */
    protected static function detectAndSetContentType($server, ?array $data = null, ?array $files = null): void
    {
        if ($files && count($files) > 0) {
            $server->set('CONTENT_TYPE', ContentType::FORM_DATA);
        } elseif ($data) {
            $server->set('CONTENT_TYPE', ContentType::URL_ENCODED);
        } else {
            $server->set('CONTENT_TYPE', ContentType::HTML);
        }
    }
}