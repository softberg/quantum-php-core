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
 * @since 2.5.0
 */

namespace Quantum\Libraries\Csrf;

use Quantum\Libraries\Session\SessionStorageInterface;
use Quantum\Exceptions\CsrfException;
use Quantum\Http\Request;

/**
 * Class Csrf
 * @package Quantum\Libraries\Csrf
 */
class Csrf
{

    /**
     * The token
     * @var string
     */
    private static $token = null;

    /**
     * Request methods
     * @var array
     */
    private static $methods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Generates the CSRF token or returns previously generated one
     * @param \Quantum\Libraries\Session\SessionStorageInterface $storage
     * @param string $key
     * @return string|null
     */
    public static function generateToken(SessionStorageInterface $storage, string $key): ?string
    {
        if (self::$token == null) {
            self::deleteToken($storage);
            self::$token = base64_encode($key);
            self::setToken($storage, self::$token);
        }

        return self::$token;
    }

    /**
     * Checks the token
     * @param \Quantum\Http\Request $request
     * @param \Quantum\Libraries\Session\SessionStorageInterface $storage
     * @return bool
     * @throws \Quantum\Exceptions\CsrfException
     */
    public static function checkToken(Request $request, SessionStorageInterface $storage): bool
    {
        if (in_array($request->getMethod(), self::$methods)) {

            $token = $request->getCSRFToken();

            if (!$token) {
                throw CsrfException::tokenNotFound();
            }

            if (self::getToken($storage) !== $token) {
                throw CsrfException::tokenNotMatched();
            }
        }

        return true;
    }

    /**
     * Deletes the token from storage
     * @param \Quantum\Libraries\Session\SessionStorageInterface $storage
     */
    public static function deleteToken(SessionStorageInterface $storage)
    {
        $storage->delete('token');
        self::$token = null;
    }

    /**
     * Gets the token from storage
     * @param SessionStorageInterface $storage
     * @return string|null
     */
    public static function getToken(SessionStorageInterface $storage): ?string
    {
        return $storage->get('token');
    }

    /**
     * Sets the token into the storage
     * @param \Quantum\Libraries\Session\SessionStorageInterface $storage
     * @param string $token
     */
    private static function setToken(SessionStorageInterface $storage, string $token)
    {
        $storage->set('token', $token);
    }
}
