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

namespace Quantum\Cache\Traits;

use DateTimeImmutable;
use DateInterval;

/**
 * Trait CacheTrait
 * @package Quantum\Cache\Traits
 */
trait CacheTrait
{
    /**
     * @var int
     */
    protected $ttl;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * Gets the hashed key
     */
    protected function keyHash(string $key): string
    {
        return sha1($this->prefix . $key);
    }

    /**
     * Normalizes the TTL
     * @param int|DateInterval|null $ttl
     * @return int
     */
    protected function normalizeTtl($ttl): int
    {
        if ($ttl instanceof DateInterval) {
            $now = new DateTimeImmutable();
            $future = $now->add($ttl);

            return max(0, $future->getTimestamp() - $now->getTimestamp());
        }

        if ($ttl === null) {
            return $this->ttl;
        }

        return (int) $ttl;
    }
}
