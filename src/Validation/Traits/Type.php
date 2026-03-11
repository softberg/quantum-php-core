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
 * Trait Type
 * @package Quantum\Validation\Rules
 */
trait Type
{
    /**
     * Checks the alpha characters
     */
    protected function alpha(string $value): bool
    {
        return preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $value) === 1;
    }

    /**
     * Checks the alpha and numeric characters
     */
    protected function alphaNumeric(string $value): bool
    {
        return preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $value) === 1;
    }

    /**
     * Checks the alpha and dash characters
     */
    protected function alphaDash(string $value): bool
    {
        return preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ_-])+$/i', $value) === 1;
    }

    /**
     * Checks the alphanumeric and space characters
     */
    protected function alphaSpace(string $value): bool
    {
        return preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/i', $value) === 1;
    }

    /**
     * Checks the numeric value
     */
    protected function numeric(string $value): bool
    {
        return is_numeric($value);
    }

    /**
     * Checks the integer value
     */
    protected function integer(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Checks the float value
     */
    protected function float(string $value): bool
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
    protected function minNumeric(string $value, $minValue): bool
    {
        return is_numeric($value) && is_numeric($minValue) && ($value >= $minValue);
    }

    /**
     * Determines if the provided numeric value is higher to a specific value
     * @param $maxValue
     */
    protected function maxNumeric(string $value, $maxValue): bool
    {
        return is_numeric($value) && is_numeric($maxValue) && ($value <= $maxValue);
    }
}
