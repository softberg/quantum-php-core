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
 * @since 2.3.0
 */

namespace Quantum\Exceptions;

/**
 * AuthException class
 *
 * @package Quantum
 * @category Exceptions
 */
class CryptorException extends \Exception
{
    /**
     * Open SSL Public key not created yet
     */
    const OPENSSL_PUBLIC_KEY_NOT_CREATED = 'Public key not created yet';

    /**
     * Open SSL Private key not created yet
     */
    const OPENSSL_PRIVATE_KEY_NOT_CREATED = 'Private key not created yet';

    /**
     * Open SSL Public key is not provided
     */
    const OPENSSL_PUBLIC_KEY_NOT_PROVIDED = 'Public key is not provided';

    /**
     * Open SSL Private key is not provided
     */
    const OPENSSL_PRIVATE_KEY_NOT_PROVIDED = 'Private key is not provided';

    /**
     * Open SSL chiper is invalid
     */
    const OPENSSEL_INVALID_CIPHER = 'The cipher is invalid';

    /**
     * Open SSL config not found
     */
    const OPENSSEL_CONFIG_NOT_FOUND = 'Could not load openssl.cnf properly.';
}
