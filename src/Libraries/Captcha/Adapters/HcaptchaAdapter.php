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
 * @since 2.9.0
 */

namespace Quantum\Libraries\Captcha\Adapters;

use Quantum\Libraries\Curl\HttpClient;

/**
 * Class HcaptchaAdapter
 * @package Quantum\Libraries\Captcha\Adapters
 */
class HcaptchaAdapter extends BaseCaptcha
{

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
     * @var HcaptchaAdapter
     */
    private static $instance = null;

    /**
     * Hcaptcha constructor
     * @param array $params
     * @param HttpClient $httpClient
     */
    private function __construct(array $params, HttpClient $httpClient)
    {
        $this->http = $httpClient;

        $this->secretKey = $params['secret_key'];
        $this->siteKey = $params['site_key'];
        $this->type = $params['type'] ?? null;
    }

    /**
     * Get Instance
     * @param array $params
     * @param HttpClient $httpClient
     * @return HcaptchaAdapter
     */
    public static function getInstance(array $params, HttpClient $httpClient): HcaptchaAdapter
    {
        if (self::$instance === null) {
            self::$instance = new self($params, $httpClient);
        }

        return self::$instance;
    }

    /**
     * @return void
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }

}