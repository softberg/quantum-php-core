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

use Quantum\Exceptions\StopExecutionException;
use Quantum\Libraries\Session\SessionManager;
use Quantum\Libraries\Encryption\Cryptor;
use Quantum\Libraries\Auth\AuthManager;
use Quantum\Libraries\Session\Session;
use Quantum\Libraries\Mailer\Mailer;
use Quantum\Libraries\Cookie\Cookie;
use Quantum\Libraries\Csrf\Csrf;
use Quantum\Loader\Loader;
use Quantum\Di\Di;

if (!function_exists('session')) {

    /**
     * Gets the session handler
     * @return \Quantum\Libraries\Session\Session
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\ModelException
     * @throws \ReflectionException
     */
    function session(): Session
    {
        return SessionManager::getHandler(Di::get(Loader::class));
    }

}

if (!function_exists('cookie')) {

    /**
     * Gets cookie handler
     * @return Quantum\Libraries\Cookie\Cookie
     */
    function cookie(): Cookie
    {
        return new Cookie($_COOKIE, new Cryptor);
    }

}

if (!function_exists('auth')) {

    /**
     * Gets the Auth handler
     * @return \Quantum\Libraries\Auth\ApiAuth|\Quantum\Libraries\Auth\WebAuth
     * @throws \Quantum\Exceptions\AuthException
     * @throws \Quantum\Exceptions\ConfigException
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\LoaderException
     * @throws \ReflectionException
     */
    function auth()
    {
        return AuthManager::getHandler(Di::get(Loader::class));
    }

}

if (!function_exists('mailer')) {

    /**
     * Gets the Mail instance
     * @return \Quantum\Libraries\Mailer\Mailer
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    function mailer(): Mailer
    {
        return Di::get(Mailer::class);
    }

}

if (!function_exists('csrf_token')) {

    /**
     * Outputs generated CSRF token
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\ModelException
     * @throws \ReflectionException
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
     * @param string|array $params
     * @return string
     */
    function _message(string $subject, $params): string
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

if (!function_exists('valid_base64')) {

    /**
     * Validates base64 string
     * @param string $string
     * @return boolean
     */
    function valid_base64(string $string): bool
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
    function get_directory_classes(string $path): array
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
     * @return string|null
     */
    function get_caller_class(int $index = 2): ?string
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
     * @return string|null
     */
    function get_caller_function(int $index = 2): ?string
    {
        $caller = debug_backtrace();
        $caller = $caller[$index];

        return $caller['function'] ?? null;
    }

}

if (!function_exists('stop')) {

    /**
     * Throws Stop Execution Exception
     * @throws \Quantum\Exceptions\StopExecutionException
     */
    function stop()
    {
        throw new StopExecutionException(StopExecutionException::EXECUTION_TERMINATED);
    }

}

if (!function_exists('random_number')) {

    /**
     * Generates random number sequence
     * @param int $length
     * @return int
     */
    function random_number(int $length = 10): int
    {
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= rand(0, 9);
        }
        return (int) $randomString;
    }

}