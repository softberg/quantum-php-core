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

namespace Quantum\Storage\Contracts;

use Quantum\HttpClient\HttpClient;

/**
 * Interface CloudAppInterface
 * @package Quantum\Storage
 */
interface CloudAppInterface
{
    public function __construct(
        string $appKey,
        string $appSecret,
        TokenServiceInterface $tokenService,
        HttpClient $httpClient
    );

    /**
     * Send request
     * @param array<string, mixed>|string|null $data
     * @param array<string, string> $headers
     * @return mixed
     */
    public function sendRequest(
        string $url,
        $data = null,
        array $headers = [],
        string $method = 'POST'
    );
}
