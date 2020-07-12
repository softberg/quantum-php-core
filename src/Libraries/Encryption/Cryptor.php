<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.0.0
 */

namespace Quantum\Libraries\Encryption;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Exceptions\CryptorException;

/**
 * Class Cryptor
 * @package Quantum\Libraries\Encryption
 */
class Cryptor
{

    /**
     * Asymmetric factor
     * @var boolean 
     */
    private $asymmetric = false;

    /**
     * Application key
     * @var string 
     */
    private $appKey;

    /**
     * Public and private keys
     * @var array 
     */
    private $keys = [];

    /**
     * Digest algorithm
     * @var string
     */
    private $digestAlgorithm = 'SHA512';

    /**
     * Key type
     * @var integer 
     */
    private $privateKeyType = OPENSSL_KEYTYPE_RSA;

    /**
     * Cipher method
     * @var string 
     */
    private $cipherMethod = 'aes-256-cbc';

    /**
     * Key bites
     * @var integer 
     */
    private $privateKeyBits = 1024;

    /**
     * The Initialization Vector 
     * @var string 
     */
    private $iv = null;

    /**
     * Cryptor constructor
     * @param boolean $asymmetric
     * @return $this
     * @throws CryptorException
     */
    public function __construct($asymmetric = false)
    {
        if (!$asymmetric) {
            if (!env('APP_KEY')) {
                throw new CryptorException(ExceptionMessages::APP_KEY_MISSING);
            }
            $this->appKey = env('APP_KEY');
        } else {
            $this->generateKeyPair();
        }

        $this->asymmetric = $asymmetric;
        return $this;
    }

    /**
     * Checks if the encryption mode is asymmetric
     * @return bool
     */
    public function isAsymmetric()
    {
        return $this->asymmetric;
    }

    /**
     * Gets the Public Key
     * @return string
     * @throws CryptorException
     */
    public function getPublicKey()
    {
        if (!isset($this->keys['public'])) {
            throw new CryptorException(ExceptionMessages::OPENSSL_PUBLIC_KEY_NOT_CREATED);
        }

        return $this->keys['public'];
    }

    /**
     * Gets the Private Key
     * @return string
     * @throws CryptorException
     */
    public function getPrivateKey()
    {
        if (!isset($this->keys['private'])) {
            throw new CryptorException(ExceptionMessages::OPENSSL_PRIVATE_KEY_NOT_CREATED);
        }

        return $this->keys['private'];
    }

    /**
     * Encrypts the plain text
     * @param string $plain
     * @param string|null $publicKey
     * @return string
     * @throws CryptorException
     */
    public function encrypt($plain, $publicKey = null)
    {
        if (!$this->isAsymmetric()) {
            $this->iv = $this->iv();
            
            $encrypted = openssl_encrypt($plain, $this->cipherMethod, $this->appKey, $options = 0, $this->iv);

            return base64_encode(base64_encode($encrypted) . '::' . base64_encode($this->iv));
        } else {
            if (!$publicKey) {
                throw new CryptorException(ExceptionMessages::OPENSSL_PUBLIC_KEY_NOT_PROVIDED);
            }

            openssl_public_encrypt($plain, $encrypted, $publicKey);
            return base64_encode($encrypted);
        }
    }

    /**
     * Decrypts the encrypted text
     * @param string $encrypted
     * @param string|null $privateKey
     * @return string
     * @throws CryptorException
     */
    public function decrypt($encrypted, $privateKey = null)
    {
        if (!$this->isAsymmetric()) {
            if (!valid_base64($encrypted)) {
                return $encrypted;
            }

            $data = explode('::', base64_decode($encrypted), 2);

            if (!$data || count($data) < 2) {
                throw new CryptorException(ExceptionMessages::OPENSSEL_INVALID_CIPHER);
            }

            $encrypted = base64_decode($data[0]);
            $iv = base64_decode($data[1]);

            return openssl_decrypt($encrypted, $this->cipherMethod, $this->appKey, $options = 0, $iv);
        } else {
            if (!$privateKey) {
                throw new CryptorException(ExceptionMessages::OPENSSL_PRIVATE_KEY_NOT_PROVIDED);
            }

            openssl_private_decrypt(base64_decode($encrypted), $decrypted, $privateKey);
            return $decrypted;
        }
    }

    /**
     * Gets the Initialization Vector used
     * @return string
     */
    public function getIV()
    {
        return $this->iv;
    }

    /**
     * Generates the Initialization Vector 
     * @return string
     */
    private function iv()
    {
        $length = openssl_cipher_iv_length($this->cipherMethod);
        return openssl_random_pseudo_bytes($length);
    }

    /**
     * Generates Key Pair
     * @return void
     * @throws CryptorException
     */
    private function generateKeyPair()
    {
        $resource = openssl_pkey_new([
            "digest_alg" => $this->digestAlgorithm,
            "private_key_type" => $this->privateKeyType,
            "private_key_bits" => $this->privateKeyBits
        ]);

        if (!$resource) {
            throw new CryptorException(ExceptionMessages::OPENSSEL_CONFIG_NOT_FOUND);
        }

        openssl_pkey_export($resource, $this->keys['private']);
        $this->keys['public'] = openssl_pkey_get_details($resource)['key'];
    }

}
