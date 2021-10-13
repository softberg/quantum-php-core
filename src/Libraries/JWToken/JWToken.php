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
 * @since 2.6.0
 */

namespace Quantum\Libraries\JWToken;

use Quantum\Exceptions\JwtException;
use Firebase\JWT\JWT;

/**
 * Class JWToken
 * @package Quantum\Libraries\JWToken
 * @uses JWT
 */
class JWToken extends JWT
{

    /**
     * JWT secret key
     * @var string
     */
    private $key;

    /**
     * Encryption algorithm
     * @var string
     */
    private $algorithm = 'HS256';

    /**
     * Payload data
     * @var array
     */
    private $payload = [];

    /**
     * @var object|null
     */
    private $fetchedPayload = null;

    /**
     * JWToken constructor.
     * @param mixed $key
     */
    public function __construct(string $key = null)
    {
        $this->key = $key ?? env('APP_KEY');
    }

    /**
     * Sets extra leeway time
     * @return $this
     */
    public function setLeeway($leeway): JWToken
    {
        parent::$leeway = $leeway;
        return $this;
    }

    /**
     * Sets the encryption algorithm
     * @param string $algorithm
     * @return $this
     */
    public function setAlgorithm(string $algorithm): JWToken
    {
        $this->algorithm = $algorithm;
        return $this;
    }

    /**
     * Sets the claim
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setClaim(string $key, $value): JWToken
    {
        $this->payload[$key] = $value;
        return $this;
    }

    /**
     * Set claims
     * @param array $claims
     * @return $this
     */
    public function setClaims(array $claims): JWToken
    {
        foreach ($claims as $key => $value) {
            $this->payload[$key] = $value;
        }

        return $this;
    }

    /**
     * Sets user data
     * @param array $data
     * @return $this
     */
    public function setData(array $data): JWToken
    {
        $this->payload['data'] = $data;
        return $this;
    }

    /**
     * Composes and signs the JWT
     * @param mixed|null $keyId
     * @param array|null $head
     * @return string
     * @throws \Quantum\Exceptions\JwtException
     */
    public function compose($keyId = null, array $head = null): string
    {
        if (empty($this->payload)) {
            throw JwtException::payloadNotFound();
        }

        return parent::encode($this->payload, $this->key, $this->algorithm, $keyId, $head);
    }

    /**
     * Retrieve and verifies the JWT
     * @param string $jwt
     * @param array $allowed_algs
     * @return $this
     */
    public function retrieve(string $jwt, array $allowed_algs = []): JWToken
    {
        $this->fetchedPayload = parent::decode($jwt, $this->key, $allowed_algs ?: [$this->algorithm]);
        return $this;
    }

    /**
     * Fetches the payload
     * @return object
     */
    public function fetchPayload(): ?object
    {
        return $this->fetchedPayload;
    }

    /**
     * Fetches the user data
     * @return array|null
     */
    public function fetchData(): ?array
    {
        return isset($this->fetchedPayload->data) ? (array) $this->fetchedPayload->data : null;
    }

    /**
     * Fetches the claim
     * @param string $key
     * @return mixed|null
     */
    public function fetchClaim(string $key)
    {
        return $this->fetchedPayload->$key ?? null;
    }

}
