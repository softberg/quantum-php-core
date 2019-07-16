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

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Storage\StorageInterface;
use Quantum\Http\Request;

/**
 * Cross-Site Request Forgery class
 *
 * @package Quantum
 * @subpackage Libraries.Csrf
 * @category Libraries
 */
class Csrf
{

    /**
     * The token
     *
     * @var string
     */
    private static $token = null;

    /**
     * Request methods
     *
     * @var array
     */
    private static $methods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Generate Token
     *
     * Generates the CSRF token or return if previously generated
     *
     * @return string
     */
    public static function generateToken(StorageInterface $storage)
    {
        if (self::$token == null) {
            if ($storage->has('token')) {
                $storage->delete('token');
            }

            self::$token = base64_encode(openssl_random_pseudo_bytes(32));

            $storage->set('token', self::$token);
        }

        return self::$token;
    }


    /**
     * Check Token
     *
     * @param Request $request
     * @param StorageInterface $storage
     * @return bool
     * @throws \Exception
     */
    public static function checkToken(Request $request, StorageInterface $storage)
    {
        if (in_array($request->getMethod(), self::$methods)) {
            $token = $request->getCSRFToken();
            if (!$token) {
                throw new \Exception(ExceptionMessages::CSRF_TOKEN_NOT_FOUND);
            }

            if ($storage->has('token') && $storage->get('token') !== $token) {
                throw new \Exception(ExceptionMessages::CSRF_TOKEN_NOT_MATCHED);
            }
        }

        return true;
    }
}
