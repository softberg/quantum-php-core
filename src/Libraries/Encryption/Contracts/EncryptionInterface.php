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

namespace Quantum\Libraries\Encryption\Contracts;

/**
 * Interface EncryptionInterface
 * @package Quantum\Libraries\Encryption
 */
interface EncryptionInterface
{

    /**
     * @param string $plain
     * @return string
     */
    public function encrypt(string $plain): string;

    /**
     * @param string $encrypted
     * @return string
     */
    public function decrypt(string $encrypted): string;
}