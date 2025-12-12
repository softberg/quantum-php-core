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
 * @since 2.9.9
 */

namespace Quantum\Libraries\Encryption\Adapters;

use Quantum\Libraries\Encryption\Contracts\EncryptionInterface;
use Quantum\Libraries\Encryption\Exceptions\CryptorException;
use Quantum\App\Exceptions\BaseException;

/**
 * Class AsymmetricEncryptionAdapter
 * @package Quantum\Libraries\Encryption
 */
class AsymmetricEncryptionAdapter implements EncryptionInterface
{

    /**
     * Digest algorithm
     */
    const DIGEST_ALGO = 'SHA512';

    /**
     * Key bits
     */
    const KEY_BITS = 1024;

    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @throws BaseException
     */
    public function __construct()
    {
        $keyPair = $this->generateKeyPair();

        $this->publicKey = $keyPair['public'];
        $this->privateKey = $keyPair['private'];
    }

    /**
     * Encrypts the string
     * @param string $plain
     * @return string
     * @throws CryptorException
     */
    public function encrypt(string $plain): string
    {
        if(!$this->publicKey) {
            throw CryptorException::publicKeyNotProvided();
        }

        openssl_public_encrypt($plain, $encrypted, $this->publicKey);
        return base64_encode($encrypted);
    }

    /**
     * Decrypts the string
     * @param string $encrypted
     * @return string
     * @throws CryptorException
     */
    public function decrypt(string $encrypted): string
    {
        if (!$this->privateKey) {
            throw CryptorException::privateKeyNotProvided();
        }

        openssl_private_decrypt(base64_decode($encrypted), $decrypted, $this->privateKey);
        return $decrypted;
    }

    /**
     * Generate key pairs
     * @return array
     * @throws BaseException
     */
    private function generateKeyPair(): array
    {
        $resource = openssl_pkey_new([
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => self::KEY_BITS,
            'digest_alg' => self::DIGEST_ALGO,
        ]);

        if (!$resource) {
            throw CryptorException::missingConfig('openssl.cnf');
        }

        openssl_pkey_export($resource, $privateKey);
        $publicKey = openssl_pkey_get_details($resource)['key'];

        return [
            'private' => $privateKey,
            'public' => $publicKey
        ];
    }
}