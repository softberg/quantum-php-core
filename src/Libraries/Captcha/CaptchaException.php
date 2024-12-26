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

use Quantum\Exceptions\AppException;

/**
 * Class CacheException
 * @package Quantum\Libraries\Captcha
 */
class CaptchaException extends AppException
{
    /**
     * @param string $name
     * @return CaptchaException
     */
    public static function unsupportedAdapter(string $name): CaptchaException
    {
        return new static(t('exception.not_supported_adapter', $name));
    }
}