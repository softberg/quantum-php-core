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

namespace Quantum\Libraries\Hasher;

use Quantum\Libraries\Hasher\Exceptions\HasherException;

/**
 * Hasher class
 * @package Quantum\Libraries\Hasher
 */
class Hasher
{
    /**
     * Default algorithm for hashing
     */
    private const DEFAULT_ALGORITHM = PASSWORD_BCRYPT;

    /**
     * Default cost for hashing
     */
    private const DEFAULT_COST = 12;

    /**
     * The algorithm
     * @var string
     */
    private $algorithm = self::DEFAULT_ALGORITHM;

    /**
     * The cost
     * @var int
     */
    private $cost = self::DEFAULT_COST;

    public function __construct()
    {
    }

    /**
     * Sets the algorithm
     * @param string $algorithm
     * @return $this
     */
    public function setAlgorithm(string $algorithm): Hasher
    {
        $this->algorithm = $algorithm;
        return $this;
    }

    /**
     * Gets the current algorithm
     * @return string
     */
    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }

    /**
     * Sets the cost
     * @param int $cost
     * @return $this
     * @throws HasherException
     */
    public function setCost(int $cost): Hasher
    {
        if ($this->algorithm === PASSWORD_BCRYPT && ($cost < 4 || $cost > 31)) {
            throw HasherException::invalidBcryptCost();
        }

        $this->cost = $cost;
        return $this;
    }

    /**
     * Gets the current cost
     * @return int
     */
    public function getCost(): int
    {
        return $this->cost;
    }

    /**
     * Hashes the given string
     * @param string $password
     * @return string|null
     */
    public function hash(string $password): ?string
    {
        return password_hash($password, $this->algorithm, ['cost' => $this->cost]);
    }

    /**
     * Checks if re-hash needed
     * @param string $hash
     * @return bool
     */
    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, $this->algorithm, ['cost' => $this->cost]);
    }

    /**
     * Checks the given string against the hash
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function check(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Gets an info of given hash
     * @param string $hash
     * @return array|null
     */
    public function info(string $hash): ?array
    {
        return password_get_info($hash);
    }
}
