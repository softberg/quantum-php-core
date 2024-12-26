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

namespace Quantum\Libraries\Captcha;

use Quantum\Libraries\Config\ConfigException;
use Quantum\Libraries\Curl\HttpClient;
use Quantum\Exceptions\DiException;
use Quantum\Loader\Setup;
use ReflectionException;

/**
 * Class CaptchaManager
 * @package Quantum\Libraries\Captcha
 */
class CaptchaManager
{
    const ADAPTERS = [
        'recaptcha',
        'hcaptcha',
    ];

    private static $adapter;

    /**
     * @return CaptchaInterface
     * @throws CaptchaException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public static function getHandler(): CaptchaInterface
    {
        if (self::$adapter !== null) {
            return self::$adapter;
        }

        if (!config()->has('captcha')) {
            config()->import(new Setup('config', 'captcha'));
        }

        $captchaAdapter = config()->get('captcha.current');

        if (!in_array($captchaAdapter, self::ADAPTERS)) {
            throw CaptchaException::unsupportedAdapter($captchaAdapter);
        }

        $captchaAdapterClassName = __NAMESPACE__ . '\\Adapters\\' . ucfirst($captchaAdapter) . 'Adapter';

        return self::$adapter = $captchaAdapterClassName::getInstance(config()->get('captcha.' . $captchaAdapter), new HttpClient);
    }
}