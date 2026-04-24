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

namespace Quantum\Http\Traits\Response;

use Quantum\App\Exceptions\StopExecutionException;
use Quantum\Http\Enums\ContentType;

/**
 * Trait Header
 * @package Quantum\Http\Response
 */
trait Header
{
    /**
     * Response headers
     * @var array<string, mixed>
     */
    private array $__headers = [];

    /**
     * Checks the response header existence by given key
     */
    public function hasHeader(string $key): bool
    {
        return isset($this->__headers[$key]);
    }

    /**
     * Gets the response header by given key
     */
    public function getHeader(string $key): ?string
    {
        return $this->hasHeader($key) ? $this->__headers[$key] : null;
    }

    /**
     * Sets the response header
     */
    public function setHeader(string $key, string $value): self
    {
        $this->__headers[$key] = $value;
        return $this;
    }

    /**
     * Get all response headers
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
            unset($this->__headers[$key]);
        }
    }

    /**
     * Sets the content type
     */
    public function setContentType(string $contentType): self
    {
        return $this->setHeader('Content-Type', $contentType);
    }

    /**
     * Gets the content type
     */
    public function getContentType(): string
    {
        return $this->getHeader('Content-Type') ?? ContentType::HTML;
    }

    /**
     * Redirect
     * @throws StopExecutionException
     */
    public function redirect(string $url, int $code = 302): void
    {
        $this->setStatusCode($code);
        $this->setHeader('Location', $url);
        stop();
    }
}
