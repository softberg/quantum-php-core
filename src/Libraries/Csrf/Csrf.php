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

namespace Quantum\Libraries\Csrf;

/**
 * Cross-Site Request Forgery class
 * 
 * @package Quantum
 * @subpackage Libraries.Csrf
 * @category Libraries
 */
class Csrf {

    /**
     * The token
     * 
     * @var string 
     */
    private static $token = NULL;

    /**
     * Generate Token
     * 
     * Generates the CSRF token or return if previously generated
     * 
     * @return string
     */
    public static function generateToken() {
        if (self::$token == NULL) {
            if (session()->has('token')) {
                session()->delete('token');
            }
            self::$token = base64_encode(openssl_random_pseudo_bytes(32));
            session()->set('token', self::$token);
        }

        return self::$token;
    }

    /**
     * Checks the token
     * 
     * Checks the session token against submitted token
     * 
     * @param string $token
     * @return boolean
     */
    public static function checkToken($token) {
        if (session()->has('token') && session()->get('token') === $token) {
            return true;
        }

        return false;
    }

}
