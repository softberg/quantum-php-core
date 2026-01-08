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
 * Trait Lists
 * @package Quantum\Libraries\Validation\Rules
 */
trait Lists
{

    /**
     * Validates that the field value is contained within a given string.
     * @param string $value
     * @param string $haystack
     * @return bool
     */
    protected function contains(string $value, string $haystack): bool
    {
        $value = trim(strtolower($value));
        $haystack = trim(strtolower($haystack));

        return strpos($haystack, $value) !== false;
    }

    /**
     * Verifies that a value is contained within the pre-defined value set.
     * @param string $value
     * @param string ...$list
     * @return bool
     */
    protected function containsList(string $value, string ...$list): bool
    {
        $value = trim(strtolower($value));

        $list = array_map(fn($item) => trim(strtolower($item)), $list);

        return in_array($value, $list);
    }

    /**
     * Verifies that a value is not contained within the pre-defined value set.
     * @param string $value
     * @param string ...$list
     * @return bool
     */
    protected function doesntContainsList(string $value, string ...$list): bool
    {
        $value = trim(strtolower($value));

        $list = array_map(fn($item) => trim(strtolower($item)), $list);

        return !in_array($value, $list);
    }
}