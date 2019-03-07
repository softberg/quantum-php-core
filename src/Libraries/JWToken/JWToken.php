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
use Quantum\Routes\RouteController;
use Firebase\JWT\JWT;

/**
 * JWToken Class
 * @package Quantum
 * @subpackage Libraries.JWTToken
 * @category Libraries 
 * @uses Firebase\JWT
 */
class JWToken extends JWT {

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
     * @var array() 
     */
    private $payload = [];

    /**
     * JWTToken instance
     * @var object 
     */
    public function __construct($key) {
        $this->key = $key;
    }

    /**
     * Sets extra leeway time
     * @return void
     */
    public function setLeeway($leeway) {
        parent::$leeway = $leeway;
    }

    /**
     * Sets the encryption algorithm
     * 
     * @param string $algorithm
     * @return void
     */
    public function setAlgorithm($algorithm) {
        $this->algorithm = $algorithm;
    }

    /**
     * Sets the payload
     * 
     * @param array $payload
     * @return void
     */
    public function setPayload(array $payload) {
        $this->payload = $payload;
    }

    /**
     * Composes and signs the JWT
     * 
     * @param mixed $keyId
     * @param mixed $head
     * @return string
     * @throws \Firebase\JWT\BeforeValidException
     */
    public function compose($keyId = null, $head = null) {
        if (!$this->payload)
            throw new \Firebase\JWT\BeforeValidException(ExceptionMessages::JWT_PAYLOAD_NOT_FOUND);

        return parent::encode($this->payload, $this->key, $this->algorithm, $keyId, $head);
    }

    /**
     * Retrieve and verifies the JWT
     * @param string $jwt
     * @param array $allowed_algs
     * @return object
     */
    public function retrieve($jwt, array $allowed_algs = array()) {
        return parent::decode($jwt, $this->key, $allowed_algs);
    }

    /**
     * Generates key
     * 
     * @return string
     */
    public static function generateSecretKey() {
        return bin2hex(openssl_random_pseudo_bytes(6)) . '-' . bin2hex(openssl_random_pseudo_bytes(12)) . '-' . bin2hex(openssl_random_pseudo_bytes(6));
    }

}
