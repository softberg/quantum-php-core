<?php

declare(strict_types=1);

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

namespace Quantum\Auth\Enums;

/**
 * Class AuthType
 * @package Quantum\Auth
 */
final class AuthType
{
    public const SESSION = 'session';

    public const JWT = 'jwt';

    private function __construct()
    {
    }
}
