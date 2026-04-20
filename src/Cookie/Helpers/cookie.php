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

use Quantum\Di\Exceptions\DiException;
use Quantum\Cookie\Cookie;
use Quantum\Di\Di;

/**
 * Gets cookie handler
 * @throws DiException|ReflectionException
 */
function cookie(): Cookie
{
    if (!Di::has(Cookie::class)) {
        Di::set(Cookie::class, new Cookie($_COOKIE));
    }

    return Di::get(Cookie::class);
}
