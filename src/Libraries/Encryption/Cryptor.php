<?php

namespace Quantum\Libraries\Encryption;

use Quantum\Exceptions\ExceptionMessages;

class Cryptor
{
    private $asymmetric = false;

    private $appKey;

    private $keys = [];

    private $digestAlgorithm = 'SHA512';

    private $privateKeyType = OPENSSL_KEYTYPE_RSA;

    private $cipherMethod = 'aes-128-cbc';

    private $privateKeyBits = 1024;


    public function __construct($asymmetric = false, $cipherMethod = 'aes-128-cbc', $keyBits = 1024)
    {
        if (!$asymmetric) {
            if (!env('APP_KEY')) {
                throw new \Exception(ExceptionMessages::APP_KEY_MISSING);
            }

            $this->appKey = env('APP_KEY');

        } else {
            $this->setCipherMethod($cipherMethod)->setKeyBits($keyBits)->generateKeyPair();
        }

        $this->asymmetric = $asymmetric;
        return $this;
    }

    public function getPublicKey()
    {
        if (!isset($this->keys['public'])) {
            throw new \Exception(ExceptionMessages::OPENSSL_PUBLIC_KEY_NOT_CREATED);
        }

        return $this->keys['public'];
    }

    public function getPrivateKey()
    {
        if (!isset($this->keys['private'])) {
            throw new \Exception(ExceptionMessages::OPENSSL_PRIVATE_KEY_NOT_CREATED);
        }

        return $this->keys['private'];
    }


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


    public function decrypt($encrypted, $privateKey = null)
    {
        if (!$this->asymmetric) {
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

    private function setKeyBits($keyBits)
    {
        $this->privateKeyBits = $keyBits;
        return $this;
    }

    private function setCipherMethod($cipherMethod)
    {
        if (!in_array($cipherMethod, openssl_get_cipher_methods())) {
            throw new \Exception(ExceptionMessages::OPENSSEL_INVALID_CIPHER);
        }

        $this->cipherMethod = $cipherMethod;
        return $this;
    }

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

    private function iv()
    {
        $length = openssl_cipher_iv_length($this->cipherMethod);
        return openssl_random_pseudo_bytes($length);
    }
}