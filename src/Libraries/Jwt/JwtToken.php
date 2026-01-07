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

namespace Quantum\Libraries\Jwt;

use Quantum\Libraries\Jwt\Exceptions\JwtException;
use Quantum\Environment\Exceptions\EnvException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Class JwtToken
 * @package Quantum\Libraries\JwtToken
 * @uses JWT
 */
class JwtToken extends JWT
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
     * JwtToken constructor.
     * @param string|null $key
     * @throws EnvException
     */
    public function __construct(string $key = null)
    {
        $this->key = $key ?? env('APP_KEY');
    }

    /**
     * Sets extra leeway time
     * @return $this
     */
    public function setLeeway($leeway): JwtToken
    {
        parent::$leeway = $leeway;
        return $this;
    }

    /**
     * Sets the encryption algorithm
     * @param string $algorithm
     * @return $this
     */
    public function setAlgorithm(string $algorithm): JwtToken
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
    public function setClaim(string $key, $value): JwtToken
    {
        $this->payload[$key] = $value;
        return $this;
    }

    /**
     * Set claims
     * @param array $claims
     * @return $this
     */
    public function setClaims(array $claims): JwtToken
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
    public function setData(array $data): JwtToken
    {
        $this->payload['data'] = $data;
        return $this;
    }

    /**
     * Composes and signs the JWT
     * @param mixed|null $keyId
     * @param array|null $head
     * @return string
     * @throws JwtException
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
     * @return $this
     */
    public function retrieve(string $jwt): JwtToken
    {
        $this->fetchedPayload = parent::decode($jwt, new Key($this->key, $this->algorithm));
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
        return isset($this->fetchedPayload->data) ? (array)$this->fetchedPayload->data : null;
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