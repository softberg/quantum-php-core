<?php

namespace Quantum\Tests\Unit\Encryption\Factories;

use Quantum\Encryption\Adapters\AsymmetricEncryptionAdapter;
use Quantum\Encryption\Adapters\SymmetricEncryptionAdapter;
use Quantum\Encryption\Contracts\EncryptionInterface;
use Quantum\Encryption\Exceptions\CryptorException;
use Quantum\Encryption\Factories\CryptorFactory;
use Quantum\Tests\Unit\AppTestCase;
use Quantum\Encryption\Cryptor;

class CryptorFactoryTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCryptorFactoryInstance(): void
    {
        $cryptor = CryptorFactory::get();

        $this->assertInstanceOf(Cryptor::class, $cryptor);
    }

    public function testCryptorFactorySymmetricAdapter(): void
    {
        $cryptor = CryptorFactory::get();

        $this->assertInstanceOf(SymmetricEncryptionAdapter::class, $cryptor->getAdapter());

        $this->assertInstanceOf(EncryptionInterface::class, $cryptor->getAdapter());
    }

    public function testCryptorFactoryAsymmetricAdapter(): void
    {
        $cryptor = CryptorFactory::get(Cryptor::ASYMMETRIC);

        $this->assertInstanceOf(AsymmetricEncryptionAdapter::class, $cryptor->getAdapter());

        $this->assertInstanceOf(EncryptionInterface::class, $cryptor->getAdapter());
    }

    public function testCryptorFactoryInvalidTypeAdapter(): void
    {
        $this->expectException(CryptorException::class);

        $this->expectExceptionMessage('The adapter `invalid_type` is not supported');

        CryptorFactory::get('invalid_type');
    }

    public function testCryptorFactoryReturnsSameInstance(): void
    {
        $cryptor1 = CryptorFactory::get();
        $cryptor2 = CryptorFactory::get();

        $this->assertSame($cryptor1, $cryptor2);
    }
}
