<?php

namespace Quantum\Tests\Unit\Libraries\Encryption;

use Quantum\Libraries\Encryption\Adapters\AsymmetricEncryptionAdapter;
use Quantum\Libraries\Encryption\Adapters\SymmetricEncryptionAdapter;
use Quantum\Libraries\Encryption\Contracts\EncryptionInterface;
use Quantum\Libraries\Encryption\Exceptions\CryptorException;
use Quantum\Libraries\Encryption\Cryptor;
use Quantum\Tests\Unit\AppTestCase;

class CryptorTest extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCryptorGetAdapter()
    {
        $cryptor = new Cryptor(new SymmetricEncryptionAdapter());

        $this->assertInstanceOf(SymmetricEncryptionAdapter::class, $cryptor->getAdapter());

        $this->assertInstanceOf(EncryptionInterface::class, $cryptor->getAdapter());

        $cryptor = new Cryptor(new AsymmetricEncryptionAdapter());

        $this->assertInstanceOf(AsymmetricEncryptionAdapter::class, $cryptor->getAdapter());

        $this->assertInstanceOf(EncryptionInterface::class, $cryptor->getAdapter());
    }

    public function testIsAsymmetric()
    {
        $cryptor = new Cryptor(new SymmetricEncryptionAdapter());

        $this->assertFalse($cryptor->isAsymmetric());

        $cryptor = new Cryptor(new AsymmetricEncryptionAdapter());

        $this->assertTrue($cryptor->isAsymmetric());
    }

    public function testCryptorCallingValidMethod()
    {
        $cryptor = new Cryptor(new SymmetricEncryptionAdapter());

        $plainText = 'The early bird gets the worm, but the second mouse gets the cheese.';

        $encrypted = $cryptor->encrypt($plainText);

        $this->assertEquals($plainText, $cryptor->decrypt($encrypted));
    }

    public function testCryptorCallingInvalidMethod()
    {
        $cryptor = new Cryptor(new SymmetricEncryptionAdapter());

        $this->expectException(CryptorException::class);

        $this->expectExceptionMessage('The method `callingInvalidMethod` is not supported for `'. SymmetricEncryptionAdapter::class .'`');

        $cryptor->callingInvalidMethod();
    }
}