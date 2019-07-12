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
 * @since 1.0.0
 */

namespace Quantum\Libraries\JWToken;

use Quantum\Exceptions\ExceptionMessages;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\JWT;

/**
 * JWToken Class
 * @package Quantum
 * @subpackage Libraries.JWTToken
 * @category Libraries
 * @uses \Firebase\JWT\
 */
class JWToken extends JWT
{

    /**
     * JWT secret key
     *
     * @var string
     */
    private $key;

    /**
     * Encryption algorithm
     *
     * @var string
     */
    private $algorithm = 'HS256';

    /**
     * Payload data
     *
     * @var array
     */
    private $payload = [];

    /**
     * Fetched payload
     *
     * @var array
     */
    private $fetchedPayload = [];

    /**
     * JWToken constructor.
     *
     * @param mixed $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Sets extra leeway time
     *
     * @return $this
     */
    public function setLeeway($leeway)
    {
        parent::$leeway = $leeway;
        return $this;
    }

    /**
     * Sets the encryption algorithm
     *
     * @param string $algorithm
     * @return $this
     */
    public function setAlgorithm($algorithm)
    {
        $this->algorithm = $algorithm;
        return $this;
    }

    /**
     * Sets the claim
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setClaim($key, $value)
    {
        $this->payload[$key] = $value;
        return $this;
    }

    /**
     * Sets user data
     *
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->payload['data'] = $data;
        return $this;
    }

    /**
     * Composes and signs the JWT
     *
     * @param mixed $keyId
     * @param mixed $head
     * @return string
     * @throws \Firebase\JWT\BeforeValidException
     */
    public function compose($keyId = null, $head = null)
    {
        if (empty($this->payload)) {
            throw new BeforeValidException(ExceptionMessages::JWT_PAYLOAD_NOT_FOUND);
        }

        return parent::encode($this->payload, $this->key, $this->algorithm, $keyId, $head);
    }

    /**
     * Retrieve and verifies the JWT
     *
     * @param string $jwt
     * @param array $allowed_algs
     * @return $this
     */
    public function retrieve($jwt, array $allowed_algs = [])
    {
        $this->fetchedPayload = parent::decode($jwt, $this->key, $allowed_algs);
        return $this;
    }

    /**
     * Fetches the payload
     *
     * @return array
     */
    public function fetchPayload()
    {
        return $this->fetchedPayload;
    }

    /**
     * Fetches the user data
     *
     * @return array|null
     */
    public function fetchData()
    {
        return isset($this->fetchedPayload->data) ? (array)$this->fetchedPayload->data : null;
    }

    /**
     * Fetches the claim
     *
     * @param string $key
     * @return mixed|null
     */
    public function fetchClaim($key)
    {
        return isset($this->fetchedPayload->$key) ? $this->fetchedPayload->$key : null;
    }

    /**
     * Generates key
     *
     * @return string
     */
    public static function generateSecretKey()
    {
        return bin2hex(openssl_random_pseudo_bytes(6)) . '-' . bin2hex(openssl_random_pseudo_bytes(12)) . '-' . bin2hex(openssl_random_pseudo_bytes(6));
    }

}
