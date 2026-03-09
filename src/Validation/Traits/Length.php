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

namespace Quantum\Validation\Traits;

/**
 * Trait Length
 * @package Quantum\Validation\Rules
 */
trait Length
{
    /**
     * Checks the min Length
     */
    protected function minLen(string $value, int $minLength): bool
    {
        return mb_strlen($value) >= $minLength;
    }

    /**
     * Checks the max Length
     */
    protected function maxLen(string $value, int $maxLength): bool
    {
        return mb_strlen($value) <= $maxLength;
    }

    /**
     * Checks the exact length
     */
    protected function exactLen(string $value, int $exactLength): bool
    {
        return mb_strlen($value) === $exactLength;
    }
}
