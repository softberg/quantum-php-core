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

namespace Quantum\Libraries\Storage\Contracts;

use Quantum\Libraries\HttpClient\HttpClient;

/**
 * Interface CloudAppInterface
 * @package Quantum\Libraries\Storage
 */
interface CloudAppInterface
{

    /**
     * @param string $appKey
     * @param string $appSecret
     * @param TokenServiceInterface $tokenService
     * @param HttpClient $httpClient
     */
    public function __construct(
        string $appKey,
        string $appSecret,
        TokenServiceInterface $tokenService,
        HttpClient $httpClient
    );

    /**
     * Send request
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param string $method
     * @return mixed
     */
    public function sendRequest(
        string $url,
        array $data = [],
        array $headers = [],
        string $method = 'POST'
    );
}