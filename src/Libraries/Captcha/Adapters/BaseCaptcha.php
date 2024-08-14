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

use Quantum\Libraries\Captcha\CaptchaInterface;
use Quantum\Libraries\Asset\AssetManager;
use Quantum\Libraries\Curl\HttpClient;
use Quantum\Exceptions\AssetException;
use Quantum\Exceptions\HttpException;
use Quantum\Exceptions\LangException;
use Quantum\Exceptions\AppException;
use Quantum\Libraries\Asset\Asset;
use Exception;

abstract class BaseCaptcha implements CaptchaInterface
{

    /**
     * @var string
     */
    protected $siteKey;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var HttpClient
     */
    protected $http;

    /**
     * @var mixed|null
     */
    protected $type = null;

    /**
     * @var array
     */
    protected $errorCodes = [];

    /**
     * @var array
     */
    protected $elementAttributes = [];

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return CaptchaInterface
     * @throws Exception
     */
    public function setType(string $type): CaptchaInterface
    {
        if (!$this->isValidCaptchaType($type)) {
            throw new Exception('Provided captcha type is not valid');
        }

        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Generates an HTML code block for the captcha
     * @param string $formIdentifier
     * @param array $attributes
     * @return string
     * @throws AssetException
     * @throws LangException
     * @throws Exception
     */
    public function addToForm(string $formIdentifier = '', array $attributes = []): string
    {
        if (!$this->type) {
            throw new Exception('Captcha type is not set');
        }

        AssetManager::getInstance()->registerAsset(new Asset(Asset::JS, static::CLIENT_API, 'captcha', -1, ['async', 'defer']));

        if (strtolower($this->type) == self::CAPTCHA_INVISIBLE) {
            return $this->getInvisibleElement($formIdentifier);
        } else {
            return $this->getVisibleElement($attributes);
        }
    }

    /**
     * Checks the code given by the captcha
     * @param string $code
     * @return bool
     * @throws LangException
     * @throws ErrorException
     * @throws AppException
     * @throws HttpException
     * @throws Exception
     */
    public function verify(string $code): bool
    {
        if (is_null($this->secretKey))
            throw new Exception('The secret key is not set');

        if (empty($code)) {
            $this->errorCodes = ['internal-empty-response'];
            return false;
        }

        $query = [
            'secret' => $this->secretKey,
            'response' => $code,
            'remoteip' => get_user_ip()
        ];

        $response = $this->http
            ->createRequest(static::VERIFY_URL . '?' . http_build_query($query))
            ->setMethod('GET')
            ->start()
            ->getResponseBody();

        if (empty($response)) {
            $this->errorCodes = ['internal-empty-response'];
            return false;
        }

        if (!is_object($response)) {
            $this->errorCodes = ['invalid-input-response'];
            return false;
        }

        if (isset($response->{'error-codes'}) && is_array($response->{'error-codes'})) {
            $this->errorCodes = $response->{'error-codes'};
        }

        if (isset($response->{'challenge_ts'}) && $this->detectReplayAttack($response->{'challenge_ts'})) {
            $this->errorCodes = ['replay-attack'];
            return false;
        }

        return isset($response->success) && $response->success;
    }

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        if (!empty($this->errorCodes)) {
            return current($this->errorCodes);
        }

        return null;
    }

    /**
     * @param array $attributes
     * @return string
     */
    protected function getVisibleElement(array $attributes = []): string
    {
        $this->extractAttributes($attributes);

        return '<div class="' . implode(' ', $this->elementClasses) . '" data-sitekey="' . $this->siteKey . '" ' . implode(' ', $this->elementAttributes) . '></div>';
    }

    /**
     * @param string $formIdentifier
     * @return string
     * @throws Exception
     */
    protected function getInvisibleElement(string $formIdentifier): string
    {
        if (empty($formIdentifier)) {
            throw new Exception('Form identifier is not provided to captcha element');
        }

        return '<script>
                 document.addEventListener("DOMContentLoaded", function() {
                     const form = document.getElementById("' . $formIdentifier . '");
                     const submitButton = form.querySelector("button[type=submit]");
                     submitButton.setAttribute("data-sitekey", "' . $this->siteKey . '");
                     submitButton.setAttribute("data-callback", "onSubmit");
                     submitButton.classList.add("' . reset($this->elementClasses) . '");
                 })
                function onSubmit (){
                    document.getElementById("' . $formIdentifier . '").submit();
                }
            </script>';
    }

    /**
     * @param $type
     * @return bool
     */
    protected function isValidCaptchaType($type): bool
    {
        $captchaTypes = [
            self::CAPTCHA_VISIBLE,
            self::CAPTCHA_INVISIBLE
        ];

        return in_array($type, $captchaTypes, true);
    }

    /**
     * @param array $attributes
     * @return void
     */
    protected function extractAttributes(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if ($key == 'class') {
                $this->elementClasses[] = $value;
            } else {
                $this->elementAttributes[] = $key . '="' . $value . '"';
            }
        }
    }

    /**
     * @param string $challengeTs
     * @return bool
     */
    protected function detectReplayAttack(string $challengeTs): bool
    {
        if (time() - strtotime($challengeTs) > self::MAX_TIME_DIFF) {
            return true;
        }

        return false;
    }

}