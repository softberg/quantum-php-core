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

use Quantum\Encryption\Factories\CryptorFactory;
use Quantum\Encryption\Enums\CryptorType;
use Quantum\App\Exceptions\BaseException;

/**
 * Encodes the data cryptographically
 * @param mixed $data
 * @throws BaseException|ReflectionException
 */
function crypto_encode($data, string $type = CryptorType::SYMMETRIC): string
{
    $serializedData = serialize($data);

    return CryptorFactory::get($type)->encrypt($serializedData);
}

/**
 * @return mixed|string
 * @throws BaseException|ReflectionException
 */
function crypto_decode(string $encryptedData, string $type = CryptorType::SYMMETRIC)
{
    $cryptor = CryptorFactory::get($type);

    $decryptedData = $cryptor->decrypt($encryptedData);

    $unSerializedData = @unserialize($decryptedData);

    if ($unSerializedData !== false) {
        return $unSerializedData;
    }

    return $decryptedData;
}
