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

namespace Quantum\Libraries\Session;

use Quantum\Libraries\Session\Contracts\SessionStorageInterface;
use Quantum\Libraries\Session\Exceptions\SessionException;
use Quantum\App\Exceptions\BaseException;

/**
 * Class Session
 * @package Quantum\Libraries\Session
 * @method array all()
 * @method bool has(string $key)
 * @method mixed|null get(string $key)
 * @method void set(string $key, $value)
 * @method mixed|null getFlash(string $key)
 * @method void setFlash(string $key, $value)
 * @method void delete(string $key)
 * @method void flush()
 * @method string|null getId()
 * @method bool regenerateId()
 */
class Session
{
    /**
     * Native session adapter
     */
    public const NATIVE = 'native';

    /**
     * Database session adapter
     */
    public const DATABASE = 'database';

    /**
     * @var SessionStorageInterface
     */
    private $adapter;

    /**
     * @param SessionStorageInterface $adapter
     */
    public function __construct(SessionStorageInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return SessionStorageInterface
     */
    public function getAdapter(): SessionStorageInterface
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
            throw SessionException::methodNotSupported($method, get_class($this->adapter));
        }

        return $this->adapter->$method(...$arguments);
    }
}
