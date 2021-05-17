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
 * Trait Length
 * @package Quantum\Libraries\Validation\Rules
 */
trait Length
{

    /**
     * Checks the min Length
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function minLen(string $field, string $value, $param = null)
    {
        if (!empty($value)) {

            $error = false;

            if (function_exists('mb_strlen')) {
                if (mb_strlen($value) < (int)$param) {
                    $error = true;
                }
            } else {
                if (strlen($value) < (int)$param) {
                    $error = true;
                }
            }

            if ($error) {
                $this->addError($field, 'minLen', $param);
            }
        }
    }

    /**
     * Checks the max Length
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function maxLen(string $field, string $value, $param = null)
    {
        if (!empty($value)) {

            $error = false;

            if (function_exists('mb_strlen')) {
                if (mb_strlen($value) > (int)$param) {
                    $error = true;
                }
            } else {
                if (strlen($value) > (int)$param) {
                    $error = true;
                }
            }

            if ($error) {
                $this->addError($field, 'maxLen', $param);
            }
        }

    }

    /**
     * Checks the exact length
     * @param string $field
     * @param string $value
     * @param null|mixed $param
     */
    protected function exactLen(string $field, string $value, $param = null)
    {
        if (!empty($value)) {

            $error = false;

            if (function_exists('mb_strlen')) {
                if (mb_strlen($value) !== (int)$param) {
                    $error = true;
                }
            } else {
                if (strlen($value) !== (int)$param) {
                    $error = true;
                }
            }

            if ($error) {
                $this->addError($field, 'exactLen', $param);
            }
        }

    }
    
}