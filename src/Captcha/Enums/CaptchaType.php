<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman@quantumphp.io>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link https://quantumphp.io/
 * @since 3.0.0
 */

namespace Quantum\Captcha\Enums;

/**
 * Class CaptchaType
 * @package Quantum\Captcha
 * @codeCoverageIgnore
 */
final class CaptchaType
{
    public const HCAPTCHA = 'hcaptcha';

    public const RECAPTCHA = 'recaptcha';

    private function __construct()
    {
    }
}
