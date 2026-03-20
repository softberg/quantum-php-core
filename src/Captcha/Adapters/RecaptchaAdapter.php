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

namespace Quantum\Captcha\Adapters;

use Quantum\Captcha\Contracts\CaptchaInterface;
use Quantum\Captcha\Traits\CaptchaTrait;
use Quantum\HttpClient\HttpClient;

/**
 * Class RecaptchaAdapter
 * @package Quantum\Captcha
 */
class RecaptchaAdapter implements CaptchaInterface
{
    use CaptchaTrait;

    public const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    public const CLIENT_API = 'https://www.google.com/recaptcha/api.js';

    /**
     * @var string
     */
    protected $name = 'g-recaptcha';

    /**
     * @var string[]
     */
    protected $elementClasses = ['g-recaptcha'];

    /**
     * RecaptchaAdapter constructor
     * @param array<string, mixed> $params
     */
    public function __construct(array $params, HttpClient $httpClient)
    {
        $this->http = $httpClient;

        $this->secretKey = $params['secret_key'];
        $this->siteKey = $params['site_key'];
        $this->type = $params['type'] ?? null;
    }
}
