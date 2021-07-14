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

use Quantum\Debugger\Debugger;
use Quantum\Factory\ViewFactory;

if (!function_exists('view')) {

    /**
     * Rendered view
     * @return string
     */
    function view()
    {
        return ViewFactory::getInstance()->getView();
    }

}

if (!function_exists('partial')) {

    /**
     * Rendered partial
     * @param string $partial
     * @param array $args
     */
    function partial($partial, $args = [])
    {
        return ViewFactory::getInstance()->renderPartial($partial, $args);
    }

}

if (!function_exists('view_param')) {

    /**
     * Gets the param passed to view
     * @param string $key
     * @return mixed|null
     */
    function view_param($key)
    {
        return ViewFactory::getInstance()->getParam($key);
    }

}

if (!function_exists('debugbar')) {

    /**
     * Rendered debug bar
     * @return string|null
     */
    function debugbar()
    {
        if (filter_var(config()->get('debug'), FILTER_VALIDATE_BOOLEAN)) {
            return (new Debugger())->render();
        }

        return null;
    }

}