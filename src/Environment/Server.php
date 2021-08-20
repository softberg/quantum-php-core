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
 * @since 2.0.0
 */

namespace Quantum\Environment;

/**
 * Class Server
 * @package Quantum\Environment
 */
class Server
{

    /**
     * @var array
     */
    private $server = [];

    /**
     * Server constructor.
     */
    public function __construct()
    {
        $this->server = $_SERVER ?? [];
    }

    /**
     * @return array
     */
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
     * @return string|null
     */
    public function uri(): ?string
    {
        return $this->server['REQUEST_URI'] ?? null;
    }

    /**
     * @return string|null
     */
    public function query(): ?string
    {
        return $this->server['QUERY_STRING'] ?? null;
    }

    /**
     * @return string|null
     */
    public function method(): ?string
    {
        return $this->server['REQUEST_METHOD'] ?? null;
    }

    /**
     * @return string|null
     */
    public function protocol(): ?string
    {
        return ((!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off') || (isset($this->server['SERVER_PORT']) && $this->server['SERVER_PORT'] == 443)) ? "https" : "http";
    }

    /**
     * @return string|null
     */
    public function host(): ?string
    {
        return $this->server['SERVER_NAME'] ?? null;
    }

    /**
     * @return string|null
     */
    public function port(): ?string
    {
        return $this->server['SERVER_PORT'] ?? null;
    }

    /**
     * @param bool $exact
     * @return string|null
     */
    public function contentType(bool $exact = false): ?string
    {
        if (isset($this->server['CONTENT_TYPE'])) {
            if ($exact && strpos($this->server['CONTENT_TYPE'], ';')) {
                return trim(substr($this->server['CONTENT_TYPE'], 0, strpos($this->server['CONTENT_TYPE'], ';')));
            }

            return $this->server['CONTENT_TYPE'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function referrer(): ?string
    {
        return $this->server['HTTP_REFERER'] ?? null;
    }

    /**
     * @return bool
     */
    public function ajax(): bool
    {
        if (!empty($this->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }

        return false;
    }

}
