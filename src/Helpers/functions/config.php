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
 * @since 2.0.0
 */

use Quantum\Libraries\Config\Config;
use Quantum\Environment\Environment;

if (!function_exists('config')) {

    /**
     * Config facade
     * @return Quantum\Libraries\Config\Config
     */
    function config(): Config
    {
        return Config::getInstance();
    }

}

if (!function_exists('env')) {

    /**
     * Gets environment variable
     * @param string $var
     * @param mixed|null $default
     * @return mixed|null
     */
    function env(string $var, $default = null)
    {
        return Environment::getInstance()->getValue($var, $default);
    }

}