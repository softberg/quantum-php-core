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
 * @since 3.0.0
 */

namespace Quantum\Libraries\Auth;

use Quantum\Libraries\Auth\Contracts\AuthenticatableInterface;
use Quantum\Libraries\Auth\Exceptions\AuthException;
use Quantum\App\Exceptions\BaseException;

/**
 * Class Auth
 * @package Quantum\Libraries\Auth
 * @method mixed signin(string $username, string $password, bool $remember = false)
 * @method bool signout()
 * @method bool check()
 * @method User user()
 */
class Auth
{
    /**
     * Web
     */
    public const SESSION = 'session';

    /**
     * Api
     */
    public const JWT = 'jwt';

    /**
     * @var AuthenticatableInterface
     */
    private $adapter;

    /**
     * @param AuthenticatableInterface $adapter
     */
    public function __construct(AuthenticatableInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return AuthenticatableInterface
     */
    public function getAdapter(): AuthenticatableInterface
    {
        return $this->adapter;
    }

    /**
     * @param string $method
     * @param array|null $arguments
     * @return mixed
     * @throws BaseException
     */
    public function __call(string $method, ?array $arguments)
    {
        if (!method_exists($this->adapter, $method)) {
            throw AuthException::methodNotSupported($method, get_class($this->adapter));
        }

        return $this->adapter->$method(...$arguments);
    }

}
