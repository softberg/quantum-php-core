<?php

namespace Quantum\Tests\Unit\Encryption\Helpers;

use Quantum\Encryption\Enums\CryptorType;
use Quantum\Tests\Unit\AppTestCase;
use stdClass;

class CryptorHelperFunctionsTest extends AppTestCase
{
    public function testCryptoEncodeAndDecodeWithSymmetricEncryption(): void
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

    public function testCryptoEncodeAndDecodeWithAsymmetricEncryption(): void
    {
        $stringValue = 'simple string';

        $encryptedString = crypto_encode($stringValue, CryptorType::ASYMMETRIC);

        $this->assertIsString($encryptedString);
        $this->assertNotEmpty($encryptedString);

        $decryptedString = crypto_decode($encryptedString, CryptorType::ASYMMETRIC);

        $this->assertIsString($decryptedString);
        $this->assertEquals($stringValue, $decryptedString);

        $data = ['key' => 'value'];

        $encryptedData = crypto_encode($data, CryptorType::ASYMMETRIC);

        $this->assertIsString($encryptedData);
        $this->assertNotEmpty($encryptedData);

        $decryptedData = crypto_decode($encryptedData, CryptorType::ASYMMETRIC);

        $this->assertIsArray($decryptedData);
        $this->assertEquals($data, $decryptedData);

        $objectValue = new stdClass();
        $objectValue->key = 'value';

        $encryptedObject = crypto_encode($objectValue, CryptorType::ASYMMETRIC);

        $this->assertIsString($encryptedObject);
        $this->assertNotEmpty($encryptedObject);

        $decryptedObject = crypto_decode($encryptedObject, CryptorType::ASYMMETRIC);

        $this->assertInstanceOf(stdClass::class, $decryptedObject);
        $this->assertEquals($objectValue, $decryptedObject);
    }
}
