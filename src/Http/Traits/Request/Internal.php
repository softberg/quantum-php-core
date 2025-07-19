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

use Quantum\App\Exceptions\BaseException;
use Quantum\Http\Constants\ContentType;
use Quantum\Environment\Server;
use ReflectionException;

/**
 * Trait Internal
 * @package Quantum\Http\Request
 */
trait Internal
{

    /**
     * @param string $method
     * @param string $url
     * @param array|null $data
     * @param array|null $files
     * @return void
     * @throws BaseException
     * @throws ReflectionException
     */
    public static function create(string $method, string $url, array $data = null, array $files = null): void
    {
        $parsed = parse_url($url);

        $server = Server::getInstance();

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

        self::detectAndSetContentType($server, $data, $files);

        static::flush();

        static::init(Server::getInstance());

        if ($data) {
            self::setRequestParams($data);
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