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

namespace Quantum\Validation\Traits;

/**
 * Trait Type
 * @package Quantum\Validation\Rules
 */
trait Type
{
    /**
     * Checks the alpha characters
     */
    protected function alpha($value): bool
    {
        return preg_match('/^[\p{L}]+$/u', (string) $value) === 1;
    }

    /**
     * Checks the alpha and numeric characters
     */
    protected function alphaNumeric($value): bool
    {
        return preg_match('/^[\p{L}0-9]+$/u', (string) $value) === 1;
    }

    /**
     * Checks the alpha and dash characters
     */
    protected function alphaDash($value): bool
    {
        return preg_match('/^[\p{L}_-]+$/u', (string) $value) === 1;
    }

    /**
     * Checks the alphanumeric and space characters
     */
    protected function alphaSpace($value): bool
    {
        return preg_match('/^[\p{L}0-9\s]+$/u', (string) $value) === 1;
    }

    /**
     * Checks the numeric value
     */
    protected function numeric($value): bool
    {
        return is_numeric($value);
    }

    /**
     * Checks the integer value
     */
    protected function integer($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Checks the float value
     */
    protected function float($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    /**
     * Checks the boolean value
     * @param $value
     */
    protected function boolean($value): bool
    {
        $booleans = ['1', 'true', true, 1, '0', 'false', false, 0, 'yes', 'no', 'on', 'off'];

        return in_array($value, $booleans, true);
    }

    /**
     * Determines if the provided numeric value is lower to a specific value
     * @param $minValue
     */
    protected function minNumeric($value, $minValue): bool
    {
        return is_numeric($value) && is_numeric($minValue) && ($value >= $minValue);
    }

    /**
     * Determines if the provided numeric value is higher to a specific value
     * @param $maxValue
     */
    protected function maxNumeric($value, $maxValue): bool
    {
        return is_numeric($value) && is_numeric($maxValue) && ($value <= $maxValue);
    }
}
