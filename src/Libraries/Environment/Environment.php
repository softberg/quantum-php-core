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
        $environment = get_config('app_env', 'production');
        
        $file = '.env';
        if($environment != 'production') {
            $file .= '.' . $environment;
        }
        
        $dotenv = new Dotenv(BASE_DIR, $file);
        $dotenv->load();
    }

}
