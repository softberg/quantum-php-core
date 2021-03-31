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
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Libraries\Lang\Lang;
use Quantum\Di\Di;

if (!function_exists('current_lang')) {

    /**
     * Gets the current lang
     * @return string
     */
    function current_lang()
    {
        return Lang::getInstance(Di::get(FileSystem::class))->getLang();
    }

}

if (!function_exists('t')) {

    /**
     * Gets translation
     * @param string $key
     * @param mixed $params
     * @return string
     */
    function t($key, $params = null)
    {
        return Lang::getInstance(Di::get(FileSystem::class))->getTranslation($key, $params);
    }

}

if (!function_exists('_t')) {

    /**
     * Outputs the translation
     * @param string $key
     * @param mixed $params
     * @return void
     */
    function _t($key, $params = null)
    {
        echo t($key, $params);
    }

}


