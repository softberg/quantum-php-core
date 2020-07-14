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

namespace Quantum\Test\Unit {

    use PHPUnit\Framework\TestCase;
    use Quantum\Libraries\Encryption\Cryptor;
    use Quantum\Exceptions\CryptorException;
    use Quantum\Exceptions\ExceptionMessages;

    class CryptorTest extends TestCase
    {

        private $cryptor;
        private $phraseOne = 'Fist Phrase';
        private $phraseTwo = 'Second Phrase';

        public function setUp(): void
        {
            $this->cryptor = new Cryptor();
        }

        public function testCryptorConstructor()
        {
            $this->assertInstanceOf('Quantum\Libraries\Encryption\Cryptor', $this->cryptor);
        }

        public function testIsAsymmetric()
        {
            $this->cryptor = new Cryptor();

            $this->assertFalse($this->cryptor->isAsymmetric());

            $this->cryptor = new Cryptor(true);

            $this->assertTrue($this->cryptor->isAsymmetric());
        }

        public function testEncryptAndDecrypt()
        {
            $this->assertFalse($this->cryptor->isAsymmetric());

            $encrypted = $this->cryptor->encrypt($this->phraseOne);

            $this->assertEquals($this->phraseOne, $this->cryptor->decrypt($encrypted));

            $this->assertNotEquals($this->phraseTwo, $this->cryptor->decrypt($encrypted));
        }

        public function testGetPublicKey()
        {
            $this->cryptor = new Cryptor(true);

            $this->assertIsString($this->cryptor->getPublicKey());

            $this->assertStringContainsString('BEGIN PUBLIC KEY', $this->cryptor->getPublicKey());
        }

        public function testGetPrivateKey()
        {
            $this->cryptor = new Cryptor(true);

            $this->assertIsString($this->cryptor->getPrivateKey());

            $this->assertStringContainsString('BEGIN PRIVATE KEY', $this->cryptor->getPrivateKey());
        }

        public function testAsymetircEncryptAndDecrypt()
        {
            $this->cryptor = new Cryptor(true);

            $this->assertTrue($this->cryptor->isAsymmetric());

            $publicKey = $this->cryptor->getPublicKey();

            $privateKey = $this->cryptor->getPrivateKey();

            $encrypted = $this->cryptor->encrypt($this->phraseOne, $publicKey);

            $this->assertEquals($this->phraseOne, $this->cryptor->decrypt($encrypted, $privateKey));

            $this->assertNotEquals($this->phraseTwo, $this->cryptor->decrypt($encrypted, $privateKey));
        }

        public function testAsymmetricEncryptionWIthMissingPublicKey()
        {
            $this->cryptor = new Cryptor(true);

            $this->expectException(CryptorException::class);

            $this->expectExceptionMessage(ExceptionMessages::OPENSSL_PUBLIC_KEY_NOT_PROVIDED);

            $this->cryptor->encrypt($this->phraseOne);
        }

        public function testAsymmetricDecryptionWIthMissingPrivateKey()
        {
            $this->cryptor = new Cryptor(true);

            $publicKey = $this->cryptor->getPublicKey();

            $encrypted = $this->cryptor->encrypt($this->phraseOne, $publicKey);

            $this->expectException(CryptorException::class);

            $this->expectExceptionMessage(ExceptionMessages::OPENSSL_PRIVATE_KEY_NOT_PROVIDED);

            $this->cryptor->decrypt($encrypted);
        }

        public function testGetIV()
        {
            $this->cryptor = new Cryptor();

            $this->assertNull($this->cryptor->getIV());

            $this->cryptor->encrypt($this->phraseOne);

            $this->assertNotNull($this->cryptor->getIV());

            $this->assertIsString($this->cryptor->getIV());
        }

    }

}