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
 * @since 2.9.5
 */

namespace Quantum\Libraries\Encryption\Adapters;

use Quantum\Libraries\Encryption\Contracts\EncryptionInterface;
use Quantum\Libraries\Encryption\Exceptions\CryptorException;
use Quantum\App\Exceptions\AppException;
use Quantum\Exceptions\BaseException;

/**
 * Class SymmetricEncryptionAdapter
 * @package Quantum\Libraries\Encryption
 */
class SymmetricEncryptionAdapter implements EncryptionInterface
{

    /**
     * Cipher method
     */
    const CIPHER_METHOD = 'aes-256-cbc';

    /**
     * @var string
     */
    private $appKey;

    /**
     * @throws BaseException
     */
    public function __construct()
    {
        $appKey = config()->get('app_key');

        if (!$appKey) {
            throw AppException::missingAppKey();
        }

        $this->appKey = $appKey;
    }

    /**
     * @param string $plain
     * @return string
     */
    public function encrypt(string $plain): string
    {
        $iv = $this->generateIV();

        $encrypted = openssl_encrypt($plain, self::CIPHER_METHOD, $this->appKey, 0, $iv);

        return base64_encode(base64_encode($encrypted) . '::' . base64_encode($iv));
    }

    /**
     * @param string $encrypted
     * @return string
     * @throws CryptorException
     */
    public function decrypt(string $encrypted): string
    {
        if (!valid_base64($encrypted)) {
            return $encrypted;
        }

        $data = explode('::', base64_decode($encrypted), 2);

        if (empty($data) || count($data) < 2) {
            throw CryptorException::invalidCipher();
        }

        $encryptedData = base64_decode($data[0]);
        $iv = base64_decode($data[1]);

        return openssl_decrypt($encryptedData, self::CIPHER_METHOD, $this->appKey, 0, $iv);
    }

    /**
     * @return string
     */
    private function generateIV(): string
    {
        $length = openssl_cipher_iv_length(self::CIPHER_METHOD);
        return openssl_random_pseudo_bytes($length);
    }
}