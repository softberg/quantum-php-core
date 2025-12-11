<?php

namespace Quantum\Tests\Unit\Libraries\Encryption\Adapters;

use Quantum\Libraries\Encryption\Adapters\AsymmetricEncryptionAdapter;
use Quantum\Libraries\Encryption\Exceptions\CryptorException;
use Quantum\Libraries\Encryption\Enums\ExceptionMessages;
use Quantum\Tests\Unit\AppTestCase;
use ReflectionClass;

class AsymmetricEncryptionAdapterTest extends AppTestCase
{

    protected $adapter;
    protected $plainText = 'The early bird gets the worm, but the second mouse gets the cheese.';

    public function setUp(): void
    {
        parent::setUp();

        $this->adapter = new AsymmetricEncryptionAdapter();
    }

    public function testAsymmetricEncryptionAdapterGetInstance()
    {
        $this->assertInstanceOf(AsymmetricEncryptionAdapter::class, $this->adapter);
    }

    public function testAsymmetricEncryptionAdapterEncryptAndDecrypt()
    {
        $encrypted = $this->adapter->encrypt($this->plainText);

        $this->assertNotEquals($this->plainText, $encrypted);

        $this->assertIsString($encrypted);

        $decrypted = $this->adapter->decrypt($encrypted);

        $this->assertEquals($this->plainText, $decrypted);
    }

    public function testAsymmetricEncryptionAdapterWithoutPublicKey()
    {
        $reflection = new ReflectionClass(AsymmetricEncryptionAdapter::class);
        $instanceProperty = $reflection->getProperty('publicKey');
        $instanceProperty->setAccessible(true);

        $adapterInstance = $reflection->newInstanceWithoutConstructor();
        $instanceProperty->setValue($adapterInstance, null);

        $this->expectException(CryptorException::class);
        $this->expectExceptionMessage(ExceptionMessages::PUBLIC_KEY_MISSING);

        $adapterInstance->encrypt("Some text");
    }

    public function testAsymmetricEncryptionAdapterDecryptWithoutPrivateKey()
    {
        $reflection = new ReflectionClass(AsymmetricEncryptionAdapter::class);
        $instanceProperty = $reflection->getProperty('privateKey');
        $instanceProperty->setAccessible(true);

        $adapterInstance = $reflection->newInstanceWithoutConstructor();
        $instanceProperty->setValue($adapterInstance, null);

        $this->expectException(CryptorException::class);
        $this->expectExceptionMessage(ExceptionMessages::PRIVATE_KEY_MISSING);

        $adapterInstance->decrypt("Some encrypted text");
    }
}