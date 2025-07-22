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
 * @since 2.9.8
 */

use Ramsey\Uuid\Uuid;

/**
 * Generate a standard v4 UUID (random)
 * @return string
 */
function uuid_random(): string
{
    return Uuid::uuid4()->toString();
}

/**
 * Generate an ordered UUID (time-based)
 * @return string
 */
function uuid_ordered(): string
{
    return Uuid::uuid1()->toString();
}
