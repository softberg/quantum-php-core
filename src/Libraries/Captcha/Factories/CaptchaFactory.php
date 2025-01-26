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

namespace Quantum\Libraries\Captcha\Factories;

use Quantum\Libraries\Captcha\Exceptions\CaptchaException;
use Quantum\Libraries\Captcha\Adapters\RecaptchaAdapter;
use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Libraries\Captcha\Adapters\HcaptchaAdapter;
use Quantum\Libraries\HttpClient\HttpClient;
use Quantum\Di\Exceptions\DiException;
use Quantum\Libraries\Captcha\Captcha;
use Quantum\Exceptions\BaseException;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class CaptchaFactory
 * @package Quantum\Libraries\Captcha
 */
class CaptchaFactory
{

    /**
     * Supported adapters
     */
    const ADAPTERS = [
        Captcha::HCAPTCHA => HcaptchaAdapter::class,
        Captcha::RECAPTCHA => RecaptchaAdapter::class,
    ];

    /**
     * @var Captcha|null
     */
    private static $instance = null;

    /**
     * @return Captcha
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public static function get(): Captcha
    {
        if (self::$instance === null) {
            return self::$instance = self::createInstance();
        }

        return self::$instance;
    }

    /**
     * @return Captcha
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    private static function createInstance(): Captcha
    {
        if (!config()->has('captcha')) {
            config()->import(new Setup('config', 'captcha'));
        }

        $adapter = config()->get('captcha.current');

        $adapterClass = self::getAdapterClass($adapter);

        return new Captcha(new $adapterClass(config()->get('captcha.' . $adapter), new HttpClient()));
    }

    /**
     * @param string $adapter
     * @return string
     * @throws BaseException
     */
    private static function getAdapterClass(string $adapter): string
    {
        if (!array_key_exists($adapter, self::ADAPTERS)) {
            throw CaptchaException::adapterNotSupported($adapter);
        }

        return self::ADAPTERS[$adapter];
    }
}