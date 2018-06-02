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

namespace Quantum\Libraries\Environment;

use Dotenv\Dotenv;

/**
 * Environment class
 * 
 * @package Quantum
 * @subpackage Libraries.Environment
 * @category Libraries
 * @uses Dotenv
 */
class Environment {

    public static function load() {
        $dotenv = new Dotenv(BASE_DIR);
        $dotenv->load();
    }

}
