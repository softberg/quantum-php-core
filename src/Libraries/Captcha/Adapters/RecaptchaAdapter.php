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

namespace Quantum\Libraries\Captcha\Adapters;

use Quantum\Libraries\Captcha\Contracts\CaptchaInterface;
use Quantum\Libraries\Captcha\Traits\CaptchaTrait;
use Quantum\Libraries\HttpClient\HttpClient;

/**
 * Class RecaptchaAdapter
 * @package Quantum\Libraries\Captcha
 */
class RecaptchaAdapter implements CaptchaInterface
{

    use CaptchaTrait;

    const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    const CLIENT_API = 'https://www.google.com/recaptcha/api.js';

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