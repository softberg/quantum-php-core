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

namespace Quantum\Libraries\Captcha\Adapters;

use Quantum\Libraries\Captcha\Contracts\CaptchaInterface;
use Quantum\Libraries\Captcha\Traits\CaptchaTrait;
use Quantum\Libraries\HttpClient\HttpClient;

/**
 * Class HcaptchaAdapter
 * @package Quantum\Libraries\Captcha
 */
class HcaptchaAdapter implements CaptchaInterface
{

    use CaptchaTrait;

    const VERIFY_URL = 'https://hcaptcha.com/siteverify';

    const CLIENT_API = 'https://hcaptcha.com/1/api.js?onload=onLoadCallback&recaptchacompat=off';

    /**
     * @var string
     */
    protected $name = 'h-captcha';

    /**
     * @var string[]
     */
    protected $elementClasses = ['h-captcha'];

    /**
     * Hcaptcha constructor
     * @param array $params
     * @param HttpClient $httpClient
     */
    public function __construct(array $params, HttpClient $httpClient)
    {
        $this->http = $httpClient;

        $this->secretKey = $params['secret_key'];
        $this->siteKey = $params['site_key'];
        $this->type = $params['type'] ?? null;
    }
}