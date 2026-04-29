<?php

namespace Quantum\Tests\Unit\Encryption\Exceptions;

use Quantum\Encryption\Exceptions\CryptorException;
use Quantum\Tests\Unit\AppTestCase;

class CryptorExceptionTest extends AppTestCase
{
    public function testPublicKeyNotProvided(): void
    {
        $exception = CryptorException::publicKeyNotProvided();

        $this->assertInstanceOf(CryptorException::class, $exception);
        $this->assertSame('Public key is not provided', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }

    public function testPrivateKeyNotProvided(): void
    {
        $exception = CryptorException::privateKeyNotProvided();

        $this->assertInstanceOf(CryptorException::class, $exception);
        $this->assertSame('Private key is not provided', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }

    public function testInvalidCipher(): void
    {
        $exception = CryptorException::invalidCipher();

        $this->assertInstanceOf(CryptorException::class, $exception);
        $this->assertSame('The cipher is invalid', $exception->getMessage());
        $this->assertSame(E_WARNING, $exception->getCode());
    }
}

