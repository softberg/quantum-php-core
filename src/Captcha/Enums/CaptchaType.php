<?php

declare(strict_types=1);

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

namespace Quantum\Captcha\Enums;

/**
 * Class CaptchaType
 * @package Quantum\Captcha
 */
final class CaptchaType
{
    public const HCAPTCHA = 'hcaptcha';

    public const RECAPTCHA = 'recaptcha';

    private function __construct()
    {
    }
}
