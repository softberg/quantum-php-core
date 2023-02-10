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
 * @since 2.9.0
 */

namespace Quantum\Libraries\Csrf;

use Quantum\Exceptions\DatabaseException;
use Quantum\Exceptions\CryptorException;
use Quantum\Exceptions\SessionException;
use Quantum\Exceptions\ConfigException;
use Quantum\Libraries\Session\Session;
use Quantum\Exceptions\CsrfException;
use Quantum\Exceptions\LangException;
use Quantum\Libraries\Hasher\Hasher;
use Quantum\Exceptions\DiException;
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
     * @throws SessionException
     * @throws LangException
     * @throws ReflectionException
     * @throws DiException
     * @throws DatabaseException
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
     * @param string|null $token
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
