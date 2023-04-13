<?php

namespace Quantum\Exceptions;

class CaptchaException extends \Exception
{
    /**
     * @param string $name
     * @return CaptchaException
     */
    public static function cantConnect(string $name): CaptchaException
    {
        return new static(t('exception.cant_connect', $name));
    }
}