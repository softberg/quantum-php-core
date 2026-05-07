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

namespace Quantum\RateLimit\Contracts;

interface RateLimitAdapterInterface
{
    public function hit(string $key, int $limit, int $interval): bool;

    public function reset(string $key, int $count = 0): void;

    public function retryAfter(string $key): int;
}
