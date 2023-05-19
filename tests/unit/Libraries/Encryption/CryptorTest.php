<?php

namespace Quantum\Libraries\Encryption {

    function env($key)
    {
        return 'somerandomstring';
    }

    function valid_base64($string)
    {
        return true;
    }

}

namespace Quantum\Tests\Libraries\Encryption {

    use Quantum\Libraries\Encryption\Cryptor;
    use Quantum\Exceptions\CryptorException;
    use Quantum\Tests\AppTestCase;

    class CryptorTest extends AppTestCase
    {

        private $phraseOne = 'Fist Phrase';
        private $phraseTwo = 'Second Phrase';

        public function setUp(): void
        {
            parent::setUp();
        }

        public function tearDown(): void
        {
            $symmetricCryptor = Cryptor::getInstance();

            $symmetricCryptoReflection = new \ReflectionClass($symmetricCryptor);

            $symmetricCryptoReflection = $symmetricCryptoReflection->getProperty('symmetricInstance');

            $symmetricCryptoReflection->setAccessible(true);

            $symmetricCryptoReflection->setValue(null, null);

            $symmetricCryptoReflection->setAccessible(false);

        }

        public function testCryptorConstructor()
        {
            $this->assertInstanceOf(Cryptor::class, Cryptor::getInstance());

            $this->assertInstanceOf(Cryptor::class, Cryptor::getInstance(true));
        }

        public function testIsAsymmetric()
        {
            $cryptor = Cryptor::getInstance();

            $this->assertFalse($cryptor->isAsymmetric());

            $cryptor = Cryptor::getInstance(true);

            $this->assertTrue($cryptor->isAsymmetric());
        }

        public function testEncryptAndDecrypt()
        {
            $cryptor = Cryptor::getInstance();

            $this->assertFalse($cryptor->isAsymmetric());

            $encrypted = $cryptor->encrypt($this->phraseOne);

            $this->assertEquals($this->phraseOne, $cryptor->decrypt($encrypted));

            $this->assertNotEquals($this->phraseTwo, $cryptor->decrypt($encrypted));
        }

        public function testGetPublicKey()
        {
            $cryptor = Cryptor::getInstance(true);

            $this->assertIsString($cryptor->getPublicKey());

            $this->assertStringContainsString('BEGIN PUBLIC KEY', $cryptor->getPublicKey());
        }

        public function testGetPrivateKey()
        {
            $cryptor = Cryptor::getInstance(true);

            $this->assertIsString($cryptor->getPrivateKey());

            $this->assertStringContainsString('BEGIN PRIVATE KEY', $cryptor->getPrivateKey());
        }

        public function testAsymetircEncryptAndDecrypt()
        {
            $cryptor = Cryptor::getInstance(true);

            $this->assertTrue($cryptor->isAsymmetric());

            $publicKey = $cryptor->getPublicKey();

            $privateKey = $cryptor->getPrivateKey();

            $encrypted = $cryptor->encrypt($this->phraseOne, $publicKey);

            $this->assertEquals($this->phraseOne, $cryptor->decrypt($encrypted, $privateKey));

            $this->assertNotEquals($this->phraseTwo, $cryptor->decrypt($encrypted, $privateKey));
        }

        public function testAsymmetricEncryptionWIthMissingPublicKey()
        {
            $cryptor = Cryptor::getInstance(true);

            $this->expectException(CryptorException::class);

            $this->expectExceptionMessage('openssl_public_key_not_provided');

            $cryptor->encrypt($this->phraseOne);
        }

        public function testAsymmetricDecryptionWIthMissingPrivateKey()
        {
            $cryptor = Cryptor::getInstance(true);

            $publicKey = $cryptor->getPublicKey();

            $encrypted = $cryptor->encrypt($this->phraseOne, $publicKey);

            $this->expectException(CryptorException::class);

            $this->expectExceptionMessage('openssl_private_key_not_provided');

            $cryptor->decrypt($encrypted);
        }

        public function testGetIV()
        {
            $cryptor = Cryptor::getInstance();

            $this->assertNull($cryptor->getIV());

            $cryptor->encrypt($this->phraseOne);

            $this->assertNotNull($cryptor->getIV());

            $this->assertIsString($cryptor->getIV());
        }

    }

}