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
 * @since 2.4.0
 */

namespace Quantum\Libraries\Validation\Rules;

/**
 * Trait Type
 * @package Quantum\Libraries\Validation\Rules
 */
trait Type
{

    /**
     * Adds validation Error
     * @param string $field
     * @param string $rule
     * @param mixed|null $param
     */
    abstract protected function addError(string $field, string $rule, $param = null);

    /**
     * Checks the alpha characters
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function alpha(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $value) === 0) {
                $this->addError($field, 'alpha', $param);
            }
        }
    }

    /**
     * Checks the alpha and numeric characters
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function alphaNumeric(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $value) === 0) {
                $this->addError($field, 'alphaNumeric', $param);
            }
        }
    }

    /**
     * Checks the alpha and dash characters
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function alphaDash(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ_-])+$/i', $value) === 0) {
                $this->addError($field, 'alphaDash', $param);
            }
        }
    }

    /**
     * Checks the alpha numeric and space characters
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function alphaSpace(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/i', $value) === 0) {
                $this->addError($field, 'alphaSpace', $param);
            }
        }


    }

    /**
     * Checks the numeric value
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function numeric(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (!is_numeric($value)) {
                $this->addError($field, 'numeric', $param);
            }
        }
    }

    /**
     * Checks the integer value
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function integer(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                $this->addError($field, 'integer', $param);
            }
        }
    }

    /**
     * Checks the float value
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function float(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
                $this->addError($field, 'float', $param);
            }
        }
    }

    /**
     * Checks the boolean value
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function boolean(string $field, string $value, $param = null)
    {

        if (!empty($value) && $value !== 0) {
            $booleans = ['1', 'true', true, 1, '0', 'false', false, 0, 'yes', 'no', 'on', 'off'];

            if (!in_array($value, $booleans, true)) {
                $this->addError($field, 'boolean', $param);
            }
        }
    }

    /**
     * Determines if the provided numeric value is lower to a specific value
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function minNumeric(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (!is_numeric($value) || !is_numeric($param) || ($value < $param)) {
                $this->addError($field, 'minNumeric', $param);
            }
        }
    }

    /**
     * Determines if the provided numeric value is higher to a specific value
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function maxNumeric(string $field, string $value, $param = null)
    {
        if (!empty($value)) {
            if (!is_numeric($value) || !is_numeric($param) || ($value > $param)) {
                $this->addError($field, 'maxNumeric', $param);
            }
        }
    }

}