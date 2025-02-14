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

namespace Quantum\Libraries\Encryption;

use Quantum\Libraries\Encryption\Adapters\AsymmetricEncryptionAdapter;
use Quantum\Libraries\Encryption\Contracts\EncryptionInterface;
use Quantum\Libraries\Encryption\Exceptions\CryptorException;
use Quantum\Exceptions\BaseException;

/**
 * Class Cryptor
 * @package Quantum\Libraries\Encryption
 * @method string encrypt(string $plain)
 * @method string decrypt(string $encrypted)
 */
class Cryptor
{

    /**
     * Symmetric
     */
    const SYMMETRIC = 'symmetric';

    /**
     * Asymmetric
     */
    const ASYMMETRIC = 'asymmetric';

    /**
     * @var EncryptionInterface
     */
    private $adapter;

    /**
     * @param EncryptionInterface $adapter
     */
    public function __construct(EncryptionInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return EncryptionInterface
     */
    public function getAdapter(): EncryptionInterface
    {
        return $this->adapter;
    }

    /**
     * @return bool
     */
    public function isAsymmetric(): bool
    {
        return $this->adapter instanceof AsymmetricEncryptionAdapter;
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
            throw CryptorException::methodNotSupported($method, get_class($this->adapter));
        }

        return $this->adapter->$method(...$arguments);
    }
}