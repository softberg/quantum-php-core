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
 * @since 1.8.0
 */

namespace Quantum\Libraries\Encryption;

use Quantum\Exceptions\ExceptionMessages;

/**
 * Class Cryptor
 * @package Quantum\Libraries\Encryption
 */
class Cryptor
{

    /**
     * @var boolean 
     */
    private $asymmetric = false;

    /**
     * @var string 
     */
    private $appKey;

    /**
     * @var array 
     */
    private $keys = [];

    /**
     * @var string
     */
    private $digestAlgorithm = 'SHA512';

    /**
     * @var integer 
     */
    private $privateKeyType = OPENSSL_KEYTYPE_RSA;

    /**
     * @var string 
     */
    private $cipherMethod = 'aes-256-cbc';

    /**
     * @var integer 
     */
    private $privateKeyBits = 1024;

    /**
     * Cryptor constructor
     * 
     * @param boolean $asymmetric
     * @return $this
     * @throws \Exception
     */
    public function __construct($asymmetric = false)
    {
        if (!$asymmetric) {
            if (!env('APP_KEY')) {
                throw new \Exception(ExceptionMessages::APP_KEY_MISSING);
            }
            $this->appKey = env('APP_KEY');
        } else {
            $this->generateKeyPair();
        }

        $this->asymmetric = $asymmetric;
        return $this;
    }

    /**
     * Get Public Key
     * 
     * @return string
     * @throws \Exception
     */
    public function getPublicKey()
    {
        if (!isset($this->keys['public'])) {
            throw new \Exception(ExceptionMessages::OPENSSL_PUBLIC_KEY_NOT_CREATED);
        }

        return $this->keys['public'];
    }

    /**
     * Get Private Key
     * 
     * @return string
     * @throws \Exception
     */
    public function getPrivateKey()
    {
        if (!isset($this->keys['private'])) {
            throw new \Exception(ExceptionMessages::OPENSSL_PRIVATE_KEY_NOT_CREATED);
        }

        return $this->keys['private'];
    }

    /**
     * Encrypt
     * 
     * @param string $plain
     * @param string|null $publicKey
     * @return string
     * @throws \Exception
     */
    public function encrypt($plain, $publicKey = null)
    {
        if (!$this->asymmetric) {
            $iv = $this->iv();
            $encrypted = openssl_encrypt($plain, $this->cipherMethod, $this->appKey, $options = 0, $iv);

            return base64_encode(base64_encode($encrypted) . '::' . base64_encode($iv));
        } else {
            if (!$publicKey) {
                throw new \Exception(ExceptionMessages::OPENSSL_PUBLIC_KEY_NOT_PROVIDED);
            }

            openssl_public_encrypt($plain, $encrypted, $publicKey);
            return base64_encode($encrypted);
        }
    }

    /**
     * Decrypt
     * 
     * @param string $encrypted
     * @param string|null $privateKey
     * @return string
     * @throws \Exception
     */
    public function decrypt($encrypted, $privateKey = null)
    {
        if (!$this->asymmetric) {
            if (!valid_base64($encrypted)) {
                return $encrypted;
            }
            
            $data = explode('::', base64_decode($encrypted), 2);

            if (!$data || count($data) < 2) {
                throw new \Exception(ExceptionMessages::OPENSSEL_INVALID_CIPHER);
            }

            $encrypted = base64_decode($data[0]);
            $iv = base64_decode($data[1]);

            return openssl_decrypt($encrypted, $this->cipherMethod, $this->appKey, $options = 0, $iv);
            
        } else {
            if (!$privateKey) {
                throw new \Exception(ExceptionMessages::OPENSSL_PRIVATE_KEY_NOT_PROVIDED);
            }

            openssl_private_decrypt(base64_decode($encrypted), $decrypted, $privateKey);
            return $decrypted;
        }
    }

    /**
     * Generate Key Pair
     * 
     * @return void
     * @throws \Exception
     */
    private function generateKeyPair()
    {
        $resource = openssl_pkey_new([
            "digest_alg" => $this->digestAlgorithm,
            "private_key_type" => $this->privateKeyType,
            "private_key_bits" => $this->privateKeyBits
        ]);

        if (!$resource) {
            throw new \Exception(ExceptionMessages::OPENSSEL_CONFIG_NOT_FOUND);
        }

        openssl_pkey_export($resource, $this->keys['private']);
        $this->keys['public'] = openssl_pkey_get_details($resource)['key'];
    }

    /**
     * Initialization Vector 
     * 
     * @return string
     */
    private function iv()
    {
        $length = openssl_cipher_iv_length($this->cipherMethod);
        return openssl_random_pseudo_bytes($length);
    }

}
