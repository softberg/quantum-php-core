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
 * @since 2.9.5
 */

namespace Quantum\Libraries\Csrf;

use Quantum\Libraries\Encryption\Exceptions\CryptorException;
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Csrf\Exceptions\CsrfException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Libraries\Session\Session;
use Quantum\Exceptions\BaseException;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Http\Request;
use ReflectionException;

/**
 * Class Csrf
 * @package Quantum\Libraries\Csrf
 */
class Csrf
{
    /**
     * Request methods to validate against
     */
    const METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Csrf token key
     */
    const TOKEN_KEY = 'csrf-token';

    /**
     * @var Csrf
     */
    private static $instance;

    /**
     * @var Session
     */
    private $storage;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     * @throws BaseException
     */
    private function __construct()
    {
        $this->storage = session();
        $this->hasher = new Hasher();
    }

    /**
     * Csrf instance
     * @return Csrf
     */
    public static function getInstance(): Csrf
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Generates the CSRF token or returns the previously generated one
     * @param string $key
     * @return string|null
     * @throws CryptorException
     */
    public function generateToken(string $key): ?string
    {
        if (!$this->storage->has(self::TOKEN_KEY)) {
            $this->storage->set(self::TOKEN_KEY, $this->hasher->hash($key));
        }

        return $this->storage->get(self::TOKEN_KEY);
    }

    /**
     * Checks the token
     * @param Request|null $request
     * @return bool
     * @throws CryptorException
     * @throws CsrfException
     */
    public function checkToken(?Request $request): bool
    {
        if (!$request->has(self::TOKEN_KEY)) {
            throw CsrfException::tokenNotFound();
        }

        if ($this->storage->get(self::TOKEN_KEY) !== $request->getCsrfToken()) {
            throw CsrfException::tokenNotMatched();
        }

        $this->storage->delete(self::TOKEN_KEY);
        $request->delete(self::TOKEN_KEY);
        $request->deleteHeader('X-' . self::TOKEN_KEY);

        return true;
    }
}