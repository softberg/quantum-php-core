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

namespace Quantum\Storage\Contracts;

/**
 * Interface TokenServiceInterface
 * @package Quantum\Storage
 */
interface TokenServiceInterface
{
    /**
     * @return string
     */
    public function getAccessToken(): string;

    /**
     * @return string
     */
    public function getRefreshToken(): string;

    /**
     * @param string $accessToken
     * @param string|null $refreshToken
     * @return bool
     */
    public function saveTokens(string $accessToken, ?string $refreshToken = null): bool;

}
