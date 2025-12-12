<?php

namespace Quantum\Tests\Unit\Libraries\Encryption\Factories;

use Quantum\Libraries\Encryption\Adapters\AsymmetricEncryptionAdapter;
use Quantum\Libraries\Encryption\Adapters\SymmetricEncryptionAdapter;
use Quantum\Libraries\Encryption\Contracts\EncryptionInterface;
use Quantum\Libraries\Encryption\Exceptions\CryptorException;
use Quantum\Libraries\Encryption\Factories\CryptorFactory;
use Quantum\Libraries\Encryption\Cryptor;
use Quantum\Tests\Unit\AppTestCase;

class CryptorFactoryTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCryptorFactoryInstance()
    {
        $cryptor = CryptorFactory::get();

        $this->assertInstanceOf(Cryptor::class, $cryptor);
    }

    public function testCryptorFactorySymmetricAdapter()
    {
        $cryptor = CryptorFactory::get();

        $this->assertInstanceOf(SymmetricEncryptionAdapter::class, $cryptor->getAdapter());

        $this->assertInstanceOf(EncryptionInterface::class, $cryptor->getAdapter());
    }

    public function testCryptorFactoryAsymmetricAdapter()
    {
        $cryptor = CryptorFactory::get(Cryptor::ASYMMETRIC);

        $this->assertInstanceOf(AsymmetricEncryptionAdapter::class, $cryptor->getAdapter());

        $this->assertInstanceOf(EncryptionInterface::class, $cryptor->getAdapter());
    }

    public function testCryptorFactoryInvalidTypeAdapter()
    {
        $this->expectException(CryptorException::class);

        $this->expectExceptionMessage('The adapter `invalid_type` is not supported');

        CryptorFactory::get('invalid_type');
    }

    public function testCryptorFactoryReturnsSameInstance()
    {
        $cryptor1 = CryptorFactory::get();
        $cryptor2 = CryptorFactory::get();

        $this->assertSame($cryptor1, $cryptor2);
    }
}