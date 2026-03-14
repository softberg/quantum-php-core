<?php

declare(strict_types=1);

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

namespace Quantum\Auth;

use Quantum\Auth\Contracts\AuthenticatableInterface;
use Quantum\Auth\Exceptions\AuthException;
use Quantum\App\Exceptions\BaseException;

/**
 * Class Auth
 * @package Quantum\Auth
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

    private AuthenticatableInterface $adapter;

    public function __construct(AuthenticatableInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getAdapter(): AuthenticatableInterface
    {
        return $this->adapter;
    }

    /**
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
