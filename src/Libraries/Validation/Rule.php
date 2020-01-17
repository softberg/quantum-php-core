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
 * @since 1.0.0
 */

namespace Quantum\Libraries\Validation;

/**
 * Rule class
 *
 * @package Quantum
 * @subpackage Libraries.Rule
 * @category Libraries
 */

class Rule {

    /**
     * Set rule
     *
     * @param mixed|null $params 
     * 
     * @return array
     */
    public static function set(string $rule, $params = null): array
    {
        if(is_null($params)) {
            $params = null;
        }

        return [$rule => $params];
    }

}