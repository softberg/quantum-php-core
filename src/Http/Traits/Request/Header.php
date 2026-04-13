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
 * Trait Header
 * @package Quantum\Http\Request
 */
trait Header
{
    /**
     * Request headers
     * @var array<string, mixed>
     */
    private array $__headers = [];

    /**
     * Checks the request header existence by given key
     */
    public function hasHeader(string $key): bool
    {
        [$keyWithHyphens, $keyWithUnderscores] = $this->normalizeHeaderKey($key);

        return isset($this->__headers[$keyWithHyphens]) || isset($this->__headers[$keyWithUnderscores]);
    }

    /**
     * Gets the request header by given key
     */
    public function getHeader(string $key): ?string
    {
        if ($this->hasHeader($key)) {
            [$keyWithHyphens, $keyWithUnderscores] = $this->normalizeHeaderKey($key);
            return $this->__headers[$keyWithHyphens] ?? $this->__headers[$keyWithUnderscores];
        }

        return null;
    }

    /**
     * Sets the request header
     * @param mixed $value
     */
    public function setHeader(string $key, $value): void
    {
        $this->__headers[strtolower($key)] = $value;
    }

    /**
     * Gets all request headers
     * @return array<string, mixed>
     */
    public function allHeaders(): array
    {
        return $this->__headers;
    }

    /**
     * Deletes the header by given key
     */
    public function deleteHeader(string $key): void
    {
        if ($this->hasHeader($key)) {
            unset($this->__headers[strtolower($key)]);
        }
    }

    /**
     * Gets Authorization Bearer token
     */
    public function getAuthorizationBearer(): ?string
    {
        $bearerToken = null;

        $authorization = (string) $this->getHeader('Authorization');

        if ($this->hasHeader('Authorization') && preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
            $bearerToken = $matches[1];
        }

        return $bearerToken;
    }

    /**
     * Gets Basic Auth Credentials
     * @return array<string, string>|null
     */
    public function getBasicAuthCredentials(): ?array
    {
        if ($this->server->has('PHP_AUTH_USER') && $this->server->has('PHP_AUTH_PW')) {
            return [
                'username' => $this->server->get('PHP_AUTH_USER'),
                'password' => $this->server->get('PHP_AUTH_PW'),
            ];
        }

        if (!$this->hasHeader('Authorization')) {
            return null;
        }

        $authorization = (string) $this->getHeader('Authorization');

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
     */
    public function isAjax(): bool
    {
        return $this->hasHeader('X-REQUESTED-WITH') || $this->server->ajax();
    }

    /**
     * Gets the referrer
     */
    public function getReferrer(): ?string
    {
        return $this->server->referrer();
    }

    /**
     * @return array<string>
     */
    private function normalizeHeaderKey(string $key): array
    {
        $keyWithHyphens = str_replace('_', '-', strtolower($key));
        $keyWithUnderscores = str_replace('-', '_', $key);

        return [$keyWithHyphens, $keyWithUnderscores];
    }
}
