<?php

namespace Quantum\Exceptions;

class CaptchaException extends \Exception
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