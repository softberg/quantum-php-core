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
 * @since 2.9.8
 */

namespace Quantum\Libraries\Validation\Traits;

/**
 * Trait Length
 * @package Quantum\Libraries\Validation\Rules
 */
trait Length
{

    /**
     * Checks the min Length
     * @param string $value
     * @param int $minLength
     * @return bool
     */
    protected function minLen(string $value, int $minLength): bool
    {
        return mb_strlen($value) >= $minLength;
    }

    /**
     * Checks the max Length
     * @param string $value
     * @param int $maxLength
     * @return bool
     */
    protected function maxLen(string $value, int $maxLength): bool
    {
        return mb_strlen($value) <= $maxLength;
    }

    /**
     * Checks the exact length
     * @param string $value
     * @param int $exactLength
     * @return bool
     */
    protected function exactLen(string $value, int $exactLength): bool
    {
        return mb_strlen($value) === $exactLength;
    }
}