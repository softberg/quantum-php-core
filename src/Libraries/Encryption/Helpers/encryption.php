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
 * @since 3.0.0
 */

use Quantum\Libraries\Encryption\Factories\CryptorFactory;
use Quantum\App\Exceptions\BaseException;
use Quantum\Libraries\Encryption\Cryptor;

/**
 * Encodes the data cryptographically
 * @param $data
 * @param string $type
 * @return string
 * @throws BaseException
 */
function crypto_encode($data, string $type = Cryptor::SYMMETRIC): string
{
    $serializedData = serialize($data);

    return CryptorFactory::get($type)->encrypt($serializedData);
}

/**
 * @param string $encryptedData
 * @param string $type
 * @return mixed|string
 * @throws BaseException
 */
function crypto_decode(string $encryptedData, string $type = Cryptor::SYMMETRIC)
{
    $cryptor = CryptorFactory::get($type);

    $decryptedData = $cryptor->decrypt($encryptedData);

    $unSerializedData = @unserialize($decryptedData);

    if ($unSerializedData !== false) {
        return $unSerializedData;
    }

    return $decryptedData;
}
