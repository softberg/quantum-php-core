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

use Quantum\Libraries\Captcha\Factories\CaptchaFactory;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Libraries\Captcha\Captcha;

/**
 * @param string|null $adapter
 * @return Captcha
 * @throws BaseException
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 */
function captcha(?string $adapter = null): Captcha
{
    return CaptchaFactory::get($adapter);
}
