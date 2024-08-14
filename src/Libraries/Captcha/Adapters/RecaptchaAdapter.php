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
 * Class RecaptchaAdapter
 * @package Quantum\Libraries\Captcha\Adapters
 */
class RecaptchaAdapter extends BaseCaptcha
{

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
     * @var RecaptchaAdapter
     */
    private static $instance = null;

    /**
     * RecaptchaAdapter constructor
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
     * @return RecaptchaAdapter
     */
    public static function getInstance(array $params, HttpClient $httpClient): RecaptchaAdapter
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