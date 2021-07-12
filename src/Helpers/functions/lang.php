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

use Quantum\Libraries\Lang\Lang;

if (!function_exists('current_lang')) {

    /**
     * Gets the current lang
     * @return string|null
     */
    function current_lang(): ?string
    {
        return Lang::getInstance()->getLang();
    }

}

if (!function_exists('t')) {

    /**
     * Gets translation
     * @param string $key
     * @param mixed $params
     * @return string
     */
    function t(string $key, $params = null): string
    {
        return Lang::getInstance()->getTranslation($key, $params);
    }

}

if (!function_exists('_t')) {

    /**
     * Outputs the translation
     * @param string $key
     * @param mixed $params
     */
    function _t(string $key, $params = null)
    {
        echo t($key, $params);
    }

}


