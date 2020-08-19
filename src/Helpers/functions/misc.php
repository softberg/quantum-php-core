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
use Quantum\Libraries\Session\SessionManager;
use Quantum\Libraries\Encryption\Cryptor;
use Quantum\Libraries\Auth\AuthManager;
use Quantum\Libraries\Mailer\Mailer;
use Quantum\Libraries\Cookie\Cookie;
use Quantum\Libraries\Dumper\Dumper;
use Quantum\Libraries\Csrf\Csrf;

if (!function_exists('session')) {

    /**
     * Gets session handler
     * @return Quantum\Libraries\Session
     */
    function session()
    {
        return (new SessionManager())->getSessionHandler();
    }

}

if (!function_exists('cookie')) {

    /**
     * Gets cookie handler
     * @return Quantum\Libraries\Cookie\Cookie
     */
    function cookie()
    {
        return new Cookie($_COOKIE, new Cryptor);
    }

}

if (!function_exists('auth')) {

    /**
     * Gets the Auth instance
     * @return WebAuth|ApiAuth|AuthenticableInterface
     */
    function auth()
    {
        return (new AuthManager())->get();
    }

}

if (!function_exists('mailer')) {

    /**
     * Gets the Mail instance
     * @return \Mail
     */
    function mailer()
    {
        return new Mailer();
    }

}

if (!function_exists('csrf_token')) {

    /**
     * Outputs generated CSRF token
     * @return void
     * @throws CsrfException
     */
    function csrf_token()
    {
        echo Csrf::generateToken(session(), env('APP_KEY'));
    }

}

if (!function_exists('_message')) {

    /**
     * _message
     * @param string $subject
     * @param string $params
     * @return string
     */
    function _message($subject, $params)
    {
        if (is_array($params)) {
            return preg_replace_callback('/{%\d+}/', function () use (&$params) {
                return array_shift($params);
            }, $subject);
        } else {
            return preg_replace('/{%\d+}/', $params, $subject);
        }
    }

}

if (!function_exists('out')) {

    /**
     * Outputs the dump of variable
     * @param mixed $var
     * @param bool
     * @return void
     */
    function out($var, $die = false)
    {
        Dumper::dump($var, $die);

        if ($die) {
            die;
        }
    }

}

if (!function_exists('valid_base64')) {

    /**
     * Validates base64 string
     * @param string $string
     * @return boolean
     */
    function valid_base64($string)
    {
        $decoded = base64_decode($string, true);

        // Check if there is no invalid character in string
        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string)) {
            return false;
        }

        // Decode the string in strict mode and send the response
        if (!base64_decode($string, true)) {
            return false;
        }

        // Encode and compare it to original one
        if (base64_encode($decoded) != $string) {
            return false;
        }

        return true;
    }

}

if (!function_exists('get_directory_classes')) {

    /**
     * Gets directory classes
     * @param string $path
     * @return array
     */
    function get_directory_classes($path)
    {
        $class_names = [];

        if (is_dir($path)) {
            $allFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
            $phpFiles = new RegexIterator($allFiles, '/\.php$/');

            foreach ($phpFiles as $file) {
                $class = pathinfo($file->getFilename());
                array_push($class_names, $class['filename']);
            }
        }

        return $class_names;
    }

}

if (!function_exists('get_caller_class')) {

    /**
     * Gets the caller class
     * @param integer $index
     * @return string
     */
    function get_caller_class($index = 2)
    {
        $caller = debug_backtrace();
        $caller = $caller[$index];

        return $caller['class'] ?? null;
    }

}

if (!function_exists('get_caller_function')) {

    /**
     * Gets the caller function
     * @param integer $index
     * @return string
     */
    function get_caller_function($index = 2)
    {
        $caller = debug_backtrace();
        $caller = $caller[$index];

        return $caller['function'] ?? null;
    }

}
