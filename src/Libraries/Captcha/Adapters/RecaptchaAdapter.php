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
     * Remote IP address
     *
     * @var string
     */
    protected $remoteIp = null;

    /**
     * Supported themes
     *
     * @var array
     * @see https://developers.google.com/recaptcha/docs/display#config
     */
    protected static $themes = array('light', 'dark');

    /**
     * Captcha theme. Default : light
     *
     * @var string
     * @see https://developers.google.com/recaptcha/docs/display#config
     */
    protected $theme = null;

    /**
     * Supported types
     *
     * @var array
     * @see https://developers.google.com/recaptcha/docs/display#config
     */
    protected static $types = array('image', 'audio');

    /**
     * Captcha type. Default : image
     *
     * @var string
     * @see https://developers.google.com/recaptcha/docs/display#config
     */
    protected $type = null;

    /**
     * Captcha language. Default : auto-detect
     *
     * @var string
     * @see https://developers.google.com/recaptcha/docs/language
     */
    protected $language = null;

    /**
     * CURL timeout (in seconds) to verify response
     *
     * @var int
     */
    private $verifyTimeout = 1;

    /**
     * Captcha size. Default : normal
     *
     * @var string
     * @see https://developers.google.com/recaptcha/docs/display#render_param
     */
    protected $size = null;

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
     * Set theme
     *
     * @param string $theme (see https://developers.google.com/recaptcha/docs/display#config)
     * @return object
     */
    public function setTheme($theme = 'light')
    {
        if (in_array($theme, self::$themes))
            $this->theme = $theme;
        else
            throw new \Exception('Theme "' . $theme . '"" is not supported. Available themes : ' . join(', ', self::$themes));

        return $this;
    }

    /**
     * Set type
     *
     * @param string $type (see https://developers.google.com/recaptcha/docs/display#config)
     * @return object
     */
    public function setType($type = 'image')
    {
        if (in_array($type, self::$types))
            $this->type = $type;

        return $this;
    }

    /**
     * Set language
     *
     * @param string $language (see https://developers.google.com/recaptcha/docs/language)
     * @return object
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Set timeout
     *
     * @param int $timeout
     * @return object
     */
    public function setVerifyTimeout($timeout)
    {
        $this->verifyTimeout = $timeout;

        return $this;
    }

    /**
     * Set size
     *
     * @param string $size (see https://developers.google.com/recaptcha/docs/display#render_param)
     * @return object
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Generate the JS code of the captcha
     *
     * @return string
     */
    public function renderJs($lang = null, $callback = false, $onLoadClass = 'onloadCallBack')
    {
        $data = array();
        if (!is_null($this->language))
            $data = array('hl' => $this->language);

        return '<script src="https://www.google.com/recaptcha/api.js?' . http_build_query($data) . '"></script>';
    }

    /**
     * Get hCaptcha js link.
     *
     * @param string $lang
     * @param boolean $callback
     * @param string $onLoadClass
     *
     * @return string
     */
    public function getJsLink($lang = null, $callback = false, $onLoadClass = 'onloadCallBack')
    {
        $client_api = static::CLIENT_API;
        $params = [];

        $callback ? $this->setCallBackParams($params, $onLoadClass) : false;
        $lang ? $params['hl'] = $lang : null;

        return $client_api . '?' . http_build_query($params);
    }

    /**
     * Generate the HTML code block for the captcha
     *
     * @return string
     */
    public function display($formIdentifier = '', $attributes = [])
    {
        if (!empty($this->sitekey)) {
            if (strtolower($this->type) == 'visible'){
                $data = 'data-sitekey="' . $this->sitekey . '"';

                if (!is_null($this->theme))
                    $data .= ' data-theme="' . $this->theme . '"';

                if (!is_null($this->type))
                    $data .= ' data-type="' . $this->type . '"';

                if (!is_null($this->size))
                    $data .= ' data-size="' . $this->size . '"';

                $captchaEleme = '<div class="col s1 offset-s2 g-recaptcha" ' . $data . '></div>';
            } elseif (strtolower($this->type) == 'invisible') {
                $captchaEleme = '';
                if (!isset($attributes['data-callback'])) {
                    $captchaEleme = '<script>
                     document.addEventListener("DOMContentLoaded", function() {
                        let button = document.getElementsByTagName("button");
                    
                        button[0].setAttribute("data-sitekey", "' . $this->sitekey . '");
                        button[0].setAttribute("data-callback", "onSubmit");
                        button[0].setAttribute("data-action", "submit");
                        button[0].classList.add("g-recaptcha");
                     })
                    
                    function onSubmit (token){
                        document.getElementById("'. $formIdentifier .'").submit();
                    }
                </script>';
                }
            }
            return $captchaEleme;
        }
    }

    /**
     * Checks the code given by the captcha
     *
     * @param string $response Response code after submitting form (usually $_POST['g-recaptcha-response'])
     * @return bool
     */
    public function verifyResponse($response, $clientIp = null)
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
    public function getErrorCodes()
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
    
}