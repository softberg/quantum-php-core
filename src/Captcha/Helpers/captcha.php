<?php

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

use Quantum\Config\Exceptions\ConfigException;
use Quantum\Captcha\Factories\CaptchaFactory;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Captcha\Captcha;

/**
 * @throws ConfigException|BaseException|DiException|ReflectionException
 */
function captcha(?string $adapter = null): Captcha
{
    return CaptchaFactory::get($adapter);
}
