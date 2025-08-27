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
 * Trait Type
 * @package Quantum\Libraries\Validation\Rules
 */
trait Type
{

    /**
     * Checks the alpha characters
     * @param string $field
     * @param string $value
     * @return bool
     */
    protected function alpha(string $field, string $value): bool
    {
        return preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $value) === 1;
    }

    /**
     * Checks the alpha and numeric characters
     * @param string $field
     * @param string $value
     * @return bool
     */
    protected function alphaNumeric(string $field, string $value): bool
    {
        return preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $value) === 1;
    }

    /**
     * Checks the alpha and dash characters
     * @param string $field
     * @param string $value
     * @return bool
     */
    protected function alphaDash(string $field, string $value): bool
    {
        return preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ_-])+$/i', $value) === 1;
    }

    /**
     * Checks the alphanumeric and space characters
     * @param string $field
     * @param string $value
     * @return bool
     */
    protected function alphaSpace(string $field, string $value): bool
    {
        return preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/i', $value) === 1;
    }

    /**
     * Checks the numeric value
     * @param string $field
     * @param string $value
     * @return bool
     */
    protected function numeric(string $field, string $value): bool
    {
        return is_numeric($value);
    }

    /**
     * Checks the integer value
     * @param string $field
     * @param string $value
     * @return bool
     */
    protected function integer(string $field, string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Checks the float value
     * @param string $field
     * @param string $value
     * @return bool
     */
    protected function float(string $field, string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    /**
     * Checks the boolean value
     * @param string $field
     * @param $value
     * @return bool
     */
    protected function boolean(string $field, $value): bool
    {
        $booleans = ['1', 'true', true, 1, '0', 'false', false, 0, 'yes', 'no', 'on', 'off'];

        return in_array($value, $booleans, true);
    }

    /**
     * Determines if the provided numeric value is lower to a specific value
     * @param string $field
     * @param string $value
     * @param $minValue
     * @return bool
     */
    protected function minNumeric(string $field, string $value, $minValue): bool
    {
        return is_numeric($value) && is_numeric($minValue) && ($value >= $minValue);
    }

    /**
     * Determines if the provided numeric value is higher to a specific value
     * @param string $field
     * @param string $value
     * @param $maxValue
     * @return bool
     */
    protected function maxNumeric(string $field, string $value, $maxValue): bool
    {
        return is_numeric($value) && is_numeric($maxValue) && ($value <= $maxValue);
    }
}