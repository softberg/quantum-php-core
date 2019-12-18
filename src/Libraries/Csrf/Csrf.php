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
use Quantum\Libraries\Session\SessionStorageInterface;
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
     * Generates the CSRF token or returns previously generated one
     *
     * @return string
     */
    public static function generateToken(SessionStorageInterface $storage, $key)
    {
        if (self::$token == null) {
            self::deleteToken($storage);
            self::$token = base64_encode($key);
            self::setToken($storage, self::$token);
        }

        return self::$token;
    }

    /**
     * Check Token
     *
     * Checks the token
     *
     * @param Request $request
     * @param SessionStorageInterface $storage
     * @return bool
     * @throws \Exception
     */
    public static function checkToken(Request $request, SessionStorageInterface $storage)
    {
        if (in_array($request->getMethod(), self::$methods)) {
            $token = $request->getCSRFToken();
            if (!$token) {
                throw new \Exception(ExceptionMessages::CSRF_TOKEN_NOT_FOUND);
            }

            if (self::getToken($storage) !== $token) {
                throw new \Exception(ExceptionMessages::CSRF_TOKEN_NOT_MATCHED);
            }
        }

        return true;
    }

    /**
     * Delete Token
     *
     * Deletes the token from storage
     *
     * @param SessionStorageInterface $storage
     * @return void
     */
    public static function deleteToken(SessionStorageInterface $storage)
    {
        $storage->delete('token');
        self::$token = null;
    }

    /**
     * Get Token
     *
     * Gets the token from storage
     *
     * @param SessionStorageInterface $storage
     * @return string|null
     */
    public static function getToken(SessionStorageInterface $storage)
    {
        return $storage->has('token') ? $storage->get('token') : null;
    }

    /**
     * Set Token
     *
     * Sets the token into the storage
     *
     * @param SessionStorageInterface $storage
     * @param string $token
     */
    private static function setToken(SessionStorageInterface $storage, $token)
    {
        $storage->set('token', $token);
    }

}
