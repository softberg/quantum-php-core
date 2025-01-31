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

use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Captcha\Factories\CaptchaFactory;
use Quantum\Di\Exceptions\DiException;
use Quantum\Libraries\Captcha\Captcha;
use Quantum\Exceptions\BaseException;

/**
 * @throws ReflectionException
 * @throws BaseException
 * @throws DiException
 * @throws ConfigException
 */
function captcha(): Captcha
{
    return CaptchaFactory::get();
}