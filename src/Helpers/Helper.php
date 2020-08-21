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
 * @since 1.9.5
 */

namespace Quantum\Helpers;

/**
 * Helper class
 *
 * @package Quantum
 * @category Helpers
 * @method string|array _message($subject, $params)
 */
class Helper
{

    /**
     * __callStatic magic
     * 
     * @param string $function
     * @param mixed|null $args
     * @return mixed
     */
    public static function __callStatic($function, $args = null)
    {
        if (function_exists($function)) {
            return $function(...$args);
        }
    }

}
