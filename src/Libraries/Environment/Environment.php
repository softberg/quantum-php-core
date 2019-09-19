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

    public static $envPath = BASE_DIR . DS . '.env';

    public static function load() {
        $env = require_once BASE_DIR . '/config/env.php';

        $envFile = '.env';

        if($env['app_env'] != 'production') {
            $envFile .= '.' . $env['app_env'];
        }

        $dotenv = new Dotenv(BASE_DIR, $envFile);
        $dotenv->load();
    }

    public static function updateRow($keyName, $value)
    {
        if($oldRow = self::getRow($keyName)) {

            file_put_contents(self::$envPath,  preg_replace(
                '/^'. $oldRow.'/m',
                $keyName . "=" . $value . "\r\n",
                file_get_contents(self::$envPath)
            ));

        } else {
            file_put_contents(self::$envPath, $keyName . "=". $value."\r\n", FILE_APPEND);
        }
    }

    private static function getRow($keyName)
    {
        $envKeys = file(self::$envPath);

        foreach ($envKeys as $envKey) {
            if(preg_match('/^'. $keyName .'=/', $envKey)) {
                return preg_quote($envKey, '/');
            }
        }
    }

}
