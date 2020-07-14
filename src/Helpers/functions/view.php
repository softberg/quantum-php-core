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

if (!function_exists('debugbar')) {

    /**
     * Rendered debug bar
     */
    function debugbar()
    {
        return ViewFactory::getInstance()->getDebugbar();
    }

}