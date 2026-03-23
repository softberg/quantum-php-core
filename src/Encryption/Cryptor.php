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

namespace Quantum\Encryption;

use Quantum\Encryption\Adapters\AsymmetricEncryptionAdapter;
use Quantum\Encryption\Contracts\EncryptionInterface;
use Quantum\Encryption\Exceptions\CryptorException;
use Quantum\App\Exceptions\BaseException;

/**
 * Class Cryptor
 * @package Quantum\Encryption
 * @method string encrypt(string $plain)
 * @method string decrypt(string $encrypted)
 */
class Cryptor
{
    private EncryptionInterface $adapter;

    public function __construct(EncryptionInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getAdapter(): EncryptionInterface
    {
        return $this->adapter;
    }

    public function isAsymmetric(): bool
    {
        return $this->adapter instanceof AsymmetricEncryptionAdapter;
    }

    /**
     * @param array<mixed>|null $arguments
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
