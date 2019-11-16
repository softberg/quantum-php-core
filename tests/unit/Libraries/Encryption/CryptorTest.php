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

        public function testEncryptAndDecrypt()
        {
            $this->assertFalse($this->cryptor->isAsymmetric());
            
            $encrypted = $this->cryptor->encrypt($this->phraseOne);

            $this->assertEquals($this->phraseOne, $this->cryptor->decrypt($encrypted));

            $this->assertNotEquals($this->phraseTwo, $this->cryptor->decrypt($encrypted));
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

    }

}