<?php

namespace Quantum\Libraries\Captcha;

use Quantum\Exceptions\CaptchaException;
use Quantum\Loader\Setup;

class CaptchaManager
{
    const ADAPTERS = [
        'recaptcha',
        'hcaptcha',
    ];

    private static $adapter;

    public static function getCaptcha() :CaptchaInterface
    {
        if (self::$adapter !== null) {
            return self::$adapter;
        }

        if (!config()->has('captcha')) {
            config()->import(new Setup('config', 'captcha'));
        }

        $captchaAdapter = config()->get('captcha.current');

        if (!in_array($captchaAdapter, self::ADAPTERS) && !is_null($captchaAdapter)) {
            throw CaptchaException::unsupportedAdapter($captchaAdapter);
        }elseif (is_null($captchaAdapter)){
            throw new \Exception('');
        }

        $captchaAdapterClassName = __NAMESPACE__ . '\\Adapters\\' . ucfirst($captchaAdapter) . 'Adapter';
        
        $params = config()->get('captcha.' . $captchaAdapter);
        return self::$adapter = $captchaAdapterClassName::getInstance($params);
    }
}