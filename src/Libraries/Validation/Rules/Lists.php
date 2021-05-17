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
 * Trait Lists
 * @package Quantum\Libraries\Validation\Rules
 */
trait Lists
{

    /**
     * Verifies that a value is contained within the pre-defined value set
     * @param string $field
     * @param string $value
     * @param string $param
     */
    protected function contains(string $field, string $value, string $param)
    {
        if (!empty($value)) {
            $param = trim(strtolower($param));
            $value = trim(strtolower($value));

            if (preg_match_all('#\'(.+?)\'#', $param, $matches, PREG_PATTERN_ORDER)) {
                $param = $matches[1];
            } else {
                $param = explode(chr(32), $param);
            }

            if (!in_array($value, $param)) {
                $this->addError($field, 'contains', null);
            }
        }
    }

    /**
     * Verifies that a value is contained within the pre-defined value set.
     * @param string $field
     * @param string $value
     * @param array $param
     */
    protected function containsList(string $field, string $value, array $param)
    {
        if (!empty($value)) {
            $param = array_map(function ($param) {
                return trim(strtolower($param));
            }, $param);

            $value = trim(strtolower($value));

            if (!in_array($value, $param)) {
                $this->addError($field, 'containsList', 'null');
            }
        }
    }

    /**
     * Verifies that a value is not contained within the pre-defined value set.
     * @param string $field
     * @param string $value
     * @param array $param
     */
    protected function doesntContainsList(string $field, string $value, array $param)
    {
        if (!empty($value)) {
            $param = array_map(function ($param) {
                return trim(strtolower($param));
            }, $param);

            $value = trim(strtolower($value));

            if (in_array($value, $param)) {
                $this->addError($field, 'doesntContainsList', null);
            }
        }
    }

}