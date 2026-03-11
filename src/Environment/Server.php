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

namespace Quantum\Environment;

/**
 * Class Server
 * @package Quantum\Environment
 */
class Server
{
    private array $server;

    private static ?Server $instance = null;

    /**
     * Server constructor.
     */
    private function __construct()
    {
        $this->server = $_SERVER;
    }

    /**
     * Get Instance
     */
    public static function getInstance(): Server
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Flushes the server params
     */
    public function flush(): void
    {
        $this->server = [];
    }

    public function all(): array
    {
        return $this->server;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function get($key)
    {
        return $this->server[$key] ?? null;
    }

    /**
     * @param $key
     */
    public function has($key): bool
    {
        return array_key_exists($key, $this->server);
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value): void
    {
        $this->server[$key] = $value;
    }

    public function uri(): ?string
    {
        return $this->get('REQUEST_URI');
    }

    public function query(): ?string
    {
        return $this->get('QUERY_STRING');
    }

    public function method(): ?string
    {
        return $this->get('REQUEST_METHOD');
    }

    public function protocol(): ?string
    {
        $https = $this->get('HTTPS');
        $port = $this->get('SERVER_PORT');

        return (!empty($https) && strtolower($https) !== 'off') || $port == 443 ? 'https' : 'http';
    }

    public function host(): ?string
    {
        return $this->get('SERVER_NAME');
    }

    public function port(): ?string
    {
        return $this->get('SERVER_PORT');
    }

    public function contentType(bool $exact = false): ?string
    {
        $contentType = $this->get('CONTENT_TYPE');

        if ($exact && $contentType && strpos($contentType, ';') !== false) {
            return trim(explode(';', $contentType, 2)[0]);
        }

        return $contentType;
    }

    public function referrer(): ?string
    {
        return $this->get('HTTP_REFERER');
    }

    public function ajax(): bool
    {
        return strtolower($this->get('HTTP_X_REQUESTED_WITH') ?? '') === 'xmlhttprequest';
    }

    public function ip(): ?string
    {
        return $this->get('HTTP_CLIENT_IP')
            ?? $this->get('HTTP_X_FORWARDED_FOR')
            ?? $this->get('REMOTE_ADDR');
    }

    public function getAllHeaders(): array
    {
        $data = $this->all();

        if ($data === []) {
            return [];
        }

        return array_reduce(array_keys($data), function (array $headers, $key) use ($data): array {
            if (strpos($key, 'HTTP_') === 0) {
                $formattedKey = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$formattedKey] = $data[$key];
            }
            return $headers;
        }, []);
    }

    public function acceptedLang(): ?string
    {
        $accept = $this->get('HTTP_ACCEPT_LANGUAGE');

        if (!$accept) {
            return null;
        }

        $first = explode(',', $accept)[0];

        return strtolower(substr(trim($first), 0, 2));
    }
}
