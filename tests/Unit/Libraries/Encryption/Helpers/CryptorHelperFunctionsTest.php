<?php

namespace Quantum\Tests\Unit\Libraries\Encryption\Helpers;

use Quantum\Libraries\Encryption\Cryptor;
use Quantum\Tests\Unit\AppTestCase;
use stdClass;

class CryptorHelperFunctionsTest extends AppTestCase
{
    public function testCryptoEncodeAndDecodeWithSymmetricEncryption()
    {
        $stringValue = 'simple string';

        $encryptedString = crypto_encode($stringValue);

        $this->assertIsString($encryptedString);
        $this->assertNotEmpty($encryptedString);

        $decryptedString = crypto_decode($encryptedString);

        $this->assertIsString($decryptedString);
        $this->assertEquals($stringValue, $decryptedString);

        $data = ['key' => 'value'];

        $encryptedData = crypto_encode($data);

        $this->assertIsString($encryptedData);
        $this->assertNotEmpty($encryptedData);

        $decryptedData = crypto_decode($encryptedData);

        $this->assertIsArray($decryptedData);
        $this->assertEquals($data, $decryptedData);

        $objectValue = new stdClass();
        $objectValue->key = 'value';

        $encryptedObject = crypto_encode($objectValue);

        $this->assertIsString($encryptedObject);
        $this->assertNotEmpty($encryptedObject);

        $decryptedObject = crypto_decode($encryptedObject);

        $this->assertInstanceOf(stdClass::class, $decryptedObject);
        $this->assertEquals($objectValue, $decryptedObject);

    }

    public function testCryptoEncodeAndDecodeWithAsymmetricEncryption()
    {
        $stringValue = 'simple string';

        $encryptedString = crypto_encode($stringValue, Cryptor::ASYMMETRIC);

        $this->assertIsString($encryptedString);
        $this->assertNotEmpty($encryptedString);

        $decryptedString = crypto_decode($encryptedString, Cryptor::ASYMMETRIC);

        $this->assertIsString($decryptedString);
        $this->assertEquals($stringValue, $decryptedString);

        $data = ['key' => 'value'];

        $encryptedData = crypto_encode($data, Cryptor::ASYMMETRIC);

        $this->assertIsString($encryptedData);
        $this->assertNotEmpty($encryptedData);

        $decryptedData = crypto_decode($encryptedData, Cryptor::ASYMMETRIC);

        $this->assertIsArray($decryptedData);
        $this->assertEquals($data, $decryptedData);

        $objectValue = new stdClass();
        $objectValue->key = 'value';

        $encryptedObject = crypto_encode($objectValue, Cryptor::ASYMMETRIC);

        $this->assertIsString($encryptedObject);
        $this->assertNotEmpty($encryptedObject);

        $decryptedObject = crypto_decode($encryptedObject, Cryptor::ASYMMETRIC);

        $this->assertInstanceOf(stdClass::class, $decryptedObject);
        $this->assertEquals($objectValue, $decryptedObject);
    }
}
