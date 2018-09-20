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


namespace Quantum\Helpers;

use Quantum\Routes\RouteController;

/**
 * Loader Class
 * 
 * Helper class is a loader class of helpers
 * 
 * @package Quantum
 * @subpackage Helpers
 * @category Helpers
 */
class Helpers {

    /**
     * Loads 3rd party helpers
     * 
     * @return void
     */
    public static function load($path = BASE_DIR) {
        $helpersDir = dirname(dirname($path)) . DS . 'helpers';
        foreach (glob($helpersDir . DS . "*.php") as $filename) {
            require_once $filename;
        }
    }

}
