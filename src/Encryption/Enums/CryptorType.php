<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman@quantumphp.io>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link https://quantumphp.io/
 * @since 3.0.0
 */

namespace Quantum\Encryption\Enums;

/**
 * Class CryptorType
 * @package Quantum\Encryption
 * @codeCoverageIgnore
 */
final class CryptorType
{
    public const SYMMETRIC = 'symmetric';

    public const ASYMMETRIC = 'asymmetric';

    private function __construct()
    {
    }
}
