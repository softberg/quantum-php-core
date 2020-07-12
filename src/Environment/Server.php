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

    private $server = [];

    public function __construct()
    {
        $this->server = $_SERVER ?? [];
    }

    public function all()
    {
        return $this->server;
    }

    public function get($key)
    {
        return $this->server[$key] ?? null;
    }

    public function uri(): ?string
    {
        return $this->server['REQUEST_URI'] ?? null;
    }

    public function query(): ?string
    {
        return $this->server['QUERY_STRING'] ?? null;
    }

    public function method(): ?string
    {
        return $this->server['REQUEST_METHOD'] ?? null;
    }

    public function protocol(): ?string
    {
        return ((!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off') || (isset($this->server['SERVER_PORT']) && $this->server['SERVER_PORT'] == 443)) ? "https" : "http";
    }

    public function host(): ?string
    {
        return $this->server['SERVER_NAME'] ?? null;
    }

    public function port(): ?string
    {
        return $this->server['SERVER_PORT'] ?? null;
    }

    public function contentType(): ?string
    {
        return $this->server['CONTENT_TYPE'] ?? null;
    }

    public function referrer(): ?string
    {
        return $this->server['HTTP_REFERER'] ?? null;
    }

    public function ajax(): bool
    {
        if (!empty($this->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }

        return false;
    }

}
