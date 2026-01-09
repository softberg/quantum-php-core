<?php

namespace Quantum\Tests\Unit\Libraries\Encryption\Adapters;

use Quantum\Libraries\Encryption\Adapters\SymmetricEncryptionAdapter;
use Quantum\Libraries\Encryption\Exceptions\CryptorException;
use Quantum\Tests\Unit\AppTestCase;

class SymmetricEncryptionAdapterTest extends AppTestCase
{
    protected $adapter;
    protected $plainText = 'The early bird gets the worm, but the second mouse gets the cheese.';

    public function setUp(): void
    {
        parent::setUp();

        $this->adapter = new SymmetricEncryptionAdapter();
    }

    public function testSymmetricEncryptionAdapterGetInstance()
    {
        $this->assertInstanceOf(SymmetricEncryptionAdapter::class, $this->adapter);
    }

    public function testSymmetricEncryptionAdapterEncryptAndDecrypt()
    {
        $encrypted = $this->adapter->encrypt($this->plainText);

        $this->assertNotEquals($this->plainText, $encrypted);

        $this->assertIsString($encrypted);

        $decrypted = $this->adapter->decrypt($encrypted);

        $this->assertEquals($this->plainText, $decrypted);
    }

    public function testSymmetricEncryptionDecryptThrowsExceptionOnInvalidData()
    {
        $this->expectException(CryptorException::class);

        $this->adapter->decrypt('invalidEncryptedData');
    }
}
