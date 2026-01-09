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

namespace Quantum\Libraries\Captcha\Factories;

use Quantum\Libraries\Captcha\Exceptions\CaptchaException;
use Quantum\Libraries\Captcha\Adapters\RecaptchaAdapter;
use Quantum\Libraries\Captcha\Adapters\HcaptchaAdapter;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Libraries\HttpClient\HttpClient;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Libraries\Captcha\Captcha;
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
    public const ADAPTERS = [
        Captcha::HCAPTCHA => HcaptchaAdapter::class,
        Captcha::RECAPTCHA => RecaptchaAdapter::class,
    ];

    /**
     * @var array<string, Captcha>
     */
    private static $instances = [];

    /**
     * @param string|null $adapter
     * @return Captcha
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public static function get(?string $adapter = null): Captcha
    {
        if (!config()->has('captcha')) {
            config()->import(new Setup('config', 'captcha'));
        }

        $adapter ??= config()->get('captcha.default');

        $adapterClass = self::getAdapterClass($adapter);

        if (!isset(self::$instances[$adapter])) {
            self::$instances[$adapter] = self::createInstance($adapterClass, $adapter);
        }

        return self::$instances[$adapter];
    }

    /**
     * @param string $adapterClass
     * @param string $adapter
     * @return Captcha
     */
    private static function createInstance(string $adapterClass, string $adapter): Captcha
    {
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
