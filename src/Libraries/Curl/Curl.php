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
 * @since 1.0.0
 */

namespace Quantum\Libraries\Curl;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Routes\RouteController;
use Curl\Curl as PhpCurl;
use Curl\MultiCurl;

/**
 * Curl Class
 * 
 * @package Quantum
 * @subpackage Libraries.Curl
 * @category Libraries 
 * @uses php-curl-class/php-curl-class
 */
class Curl {

    /**
     * Curl instance
     * @var object
     */
    private $curl;
    
    /**
     * Response body
     * @var mixed 
     */
    private $body = null;
    
    /**
     * Response headers
     * @var array
     */
    private $headers = array();
    
    /**
     * Response error
     * @var array 
     */
    private $errors = array();

    /**
     * Class constructor
     * 
     * @param string $type
     */
    public function __construct($type = null) {
        if ($type && $type == 'multi') {
            $this->curl = new MultiCurl();
        } else {
            $this->curl = new PhpCurl();
        }
    }

    /**
     * Run
     * 
     * Executes or starts the cURL request
     * 
     * @param string $url
     * @return object
     */
    public function run($url = null) {
        if ($url) {
            $this->curl->setUrl($url);
        }

        if ($this->curl instanceof MultiCurl) {
            $this->curl->start();
        } else {
            $this->curl->exec();
        }

        return $this->fetch();
    }

    /**
     * Set Options
     * 
     * Sets cURL options
     * 
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options) {
        $this->curl->setOpts($options);

        return $this;
    }

    /**
     * Set Headers
     * 
     * Sets the cURL headers
     * 
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers) {
        $this->curl->setHeaders($headers);

        return $this;
    }

    /**
     * Get Response Body
     * 
     * Gets the response body
     * 
     * @return mixed
     */
    public function getResponseBody() {
        return $this->body;
    }

    /**
     * Get Response Headers
     * 
     * Gets the response headers
     * 
     * @return array
     */
    public function getResponseHeaders() {
        $requestHeaders = [];

        if ($this->headers instanceof \ArrayAccess) {
            while ($this->headers->valid()) {
                $requestHeaders[$this->headers->key()] = $this->headers->current();
                $this->headers->next();
            }
        }

        return $requestHeaders;
    }

    /**
     * Returns the errors
     * 
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     *  __call magic
     * 
     * @param string $function
     * @param mixed $arguments
     * @return object
     */
    public function __call($function, $arguments) {

        $this->curl->{$function}(...$arguments);

        return $this->fetch();
    }

    /**
     * Fetch
     * 
     * Fetches the data from response
     * 
     * @return $this
     */
    private function fetch() {
        if ($this->curl instanceof MultiCurl) {
            $this->curl->complete(function($instance) {
                if ($instance->error) {
                    $this->errors[] = [
                        'errorCode' => $instance->getErrorCode(),
                        'errorMessage' => $instance->getErrorMessage()
                    ];
                } else {
                    $this->headers[] = $instance->getResponseHeaders();
                    $this->body[] = $instance->getResponse();
                }
            });
        } else {
            if ($this->curl->error) {
                $this->errors[] = [
                    'errorCode' => $this->curl->getErrorCode(),
                    'errorMessage' => $this->curl->getErrorMessage()
                ];
            } else {
                $this->headers = $this->curl->getResponseHeaders();
                $this->body = $this->curl->getResponse();
            }
        }

        return $this;
    }

}
