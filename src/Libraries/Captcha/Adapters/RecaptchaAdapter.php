<?php

namespace Quantum\Libraries\Captcha\Adapters;

use Quantum\Libraries\Captcha\CaptchaInterface;
use Quantum\Libraries\Curl\HttpClient;

class RecaptchaAdapter implements CaptchaInterface
{
    /**
     * ReCAPTCHA URL verifying
     *
     * @var string
     */
    const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    const CLIENT_API = 'https://www.google.com/recaptcha/api.js';

    /**
     * Public key
     *
     * @var string
     */
    private $sitekey;

    /**
     * Private key
     *
     * @var string
     */
    private $secretkey;

    /**
     * @var HttpClient
     */
    private $http;

    /**
     * Remote IP address
     *
     * @var string
     */
    protected $remoteIp = null;

    /**
     * Captcha type. Default : image
     *
     * @var string
     * @see https://developers.google.com/recaptcha/docs/display#config
     */
    protected $type = null;

    /**
     * @var RecaptchaAdapter
     */
    private static $instance = null;

    /**
     * List of errors
     *
     * @var array
     */
    protected $errorCodes = array();

    /**
     * RecaptchaAdapter
     *
     * @param array $params
     * @return void
     */
    private function __construct(array $params)
    {
        $this->http = new HttpClient();

        $this->secretkey = $params['secret_key'];
        $this->sitekey = $params['site_key'];
        $this->type = $params['type'];
    }

    /**
     * Get Instance
     * @param array $params
     * @return RecaptchaAdapter
     */
    public static function getInstance(array $params): RecaptchaAdapter
    {
        if (self::$instance === null) {
            self::$instance = new self($params);
        }

        return self::$instance;
    }

    /**
     * Generate the JS code of the captcha
     *
     * @return string
     */
    public function renderJs(): string
    {
        return '<script src="'. self::CLIENT_API .'"></script>';
    }

    /**
     * Generate the HTML code block for the captcha
     *
     * @param string $formIdentifier
     * @param array $attributes
     * @return string
     */
    public function display(string $formIdentifier = '', array $attributes = []): string
    {
        $captchaElement = '';
        if (strtolower($this->type) == 'visible'){
            $captchaElement = $this->getVisibleElement();
        } elseif (strtolower($this->type) == 'invisible') {
            $captchaElement = $this->getInvisibleElement($formIdentifier);
        }
        return $captchaElement;
    }

    /**
     * Checks the code given by the captcha
     *
     * @param string $response Response code after submitting form (usually $_POST['g-recaptcha-response'])
     * @return bool
     */
    public function verifyResponse(string $response, $clientIp = null): bool
    {
        if (is_null($this->secretkey))
            throw new \Exception('You must set your secret key');

        if (empty($response)) {

            $this->errorCodes = array('internal-empty-response');

            return false;
        }

        $query = array(
            'secret' => $this->secretkey,
            'response' => $response,
            'remoteip' => $this->remoteIp,
        );

        $url = self::VERIFY_URL . '?' . http_build_query($query);

        $this->http->createRequest($url)->setMethod('GET')->start();
        $response = (array)$this->http->getResponseBody();

        if (empty($response) || is_null($response) || !$response) {
            return false;
        }

        if (isset($response['error-codes'])) {
            $this->errorCodes = $response['error-codes'];
        }

        return $response['success'];
    }

    /**
     * Returns the errors encountered
     *
     * @return array Errors code and name
     */
    public function getErrorCodes(): array
    {
        $errors = array();

        if (count($this->errorCodes) > 0) {
            foreach ($this->errorCodes as $error) {
                switch ($error) {
                    case 'timeout-or-duplicate':
                        $errors[] = array(
                            'code' => $error,
                            'name' => 'Timeout or duplicate.',
                        );
                        break;

                    case 'missing-input-secret':
                        $errors[] = array(
                            'code' => $error,
                            'name' => 'The secret parameter is missing.',
                        );
                        break;

                    case 'invalid-input-secret':
                        $errors[] = array(
                            'code' => $error,
                            'name' => 'The secret parameter is invalid or malformed.',
                        );
                        break;

                    case 'missing-input-response':
                        $errors[] = array(
                            'code' => $error,
                            'name' => 'The response parameter is missing.',
                        );
                        break;

                    case 'invalid-input-response':
                        $errors[] = array(
                            'code' => $error,
                            'name' => 'The response parameter is invalid or malformed.',
                        );
                        break;

                    case 'bad-request':
                        $errors[] = array(
                            'code' => $error,
                            'name' => 'The request is invalid or malformed.',
                        );
                        break;

                    case 'internal-empty-response':
                        $errors[] = array(
                            'code' => $error,
                            'name' => 'The recaptcha response is required.',
                        );
                        break;

                    default:
                        $errors[] = array(
                            'code' => $error,
                            'name' => $error,
                        );
                }
            }
        }

        return $errors;
    }

    private function getInvisibleElement(string $formIdentifier): string
    {
        return '<script>
                 document.addEventListener("DOMContentLoaded", function() {
                    let button = document.getElementsByTagName("button");
                
                    button[0].setAttribute("data-sitekey", "' . $this->sitekey . '");
                    button[0].setAttribute("data-callback", "onSubmit");
                    button[0].setAttribute("data-action", "submit");
                    button[0].classList.add("g-recaptcha");
                 })
                
                function onSubmit (){
                    document.getElementById("'. $formIdentifier .'").submit();
                }
            </script>';
    }

    private function getVisibleElement(): string
    {
        $data = 'data-sitekey="' . $this->sitekey . '"';

        return '<div class="col s1 offset-s2 g-recaptcha" ' . $data . '></div>';
    }
}