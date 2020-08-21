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
 * @since 2.0.0
 */

namespace Quantum\Libraries\Curl;

use Curl\Curl as PhpCurl;
use Curl\MultiCurl;
use ArrayAccess;

/**
 * Curl Class
 * @package Quantum
 * @category Libraries 
 * @uses php-curl-class/php-curl-class
 */
class Curl
{

    /**
     * Curl instance
     * @var object
     */
    private $curl;

    /**
     * Response body
     * @var mixed 
     */
    private $responseBody = null;

    /**
     * Response headers
     * @var \Iterator
     */
    private $responseHeaders = [];

    /**
     * Response error
     * @var array 
     */
    private $errors = [];

    /**
     * Class constructor
     * @param string $type
     */
    public function __construct($type = null)
    {
        if ($type && $type == 'multi') {
            $this->curl = new MultiCurl();
        } else {
            $this->curl = new PhpCurl();
        }
    }

    /**
     * Sets the options
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->curl->setOpts($options);

        return $this;
    }

    /**
     * Gets the option
     * @param int $option
     * @return mixed
     */
    public function getOption($option)
    {
        return $this->curl->getOpt($option);
    }

    /**
     * Sets the request headers
     * @param array $headers
     * @return $this
     */
    public function setRequestHeaders(array $headers)
    {
        $this->curl->setHeaders($headers);

        return $this;
    }

    /**
     * Gets single request header or all headers after curl exec
     * @param string $header
     * @return mixed
     */
    public function getRequestHeaders($header = null)
    {
        $requestHeaders = $this->curl->getRequestHeaders();

        if ($header) {
            return $requestHeaders[$header] ?? null;
        }

        return $requestHeaders;
    }

    /**
     * Executes or starts the cURL request
     * @param string $url
     * @return object
     */
    public function run($url = null)
    {
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
     * Gets the response body
     * @return mixed
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * Gets the response headers
     * @return array
     */
    public function getResponseHeaders($header = null)
    {
        $responseHeaders = [];

        while ($this->responseHeaders->valid()) {
            $responseHeaders[strtolower($this->responseHeaders->key())] = $this->responseHeaders->current();
            $this->responseHeaders->next();
        }

        $this->responseHeaders->rewind();

        if ($header) {
            return $responseHeaders[$header] ?? null;
        }

        return $responseHeaders;
    }

    /**
     * Gets the curl info
     * @param $option
     * @return mixed
     */
    public function info($option = null)
    {
        if ($option) {
            return $this->curl->getInfo($option);
        }

        return $this->curl->getInfo();
    }

    /**
     * Returns the errors
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     *  __call magic
     * @param string $function
     * @param mixed $arguments
     * @return object
     */
    public function __call($function, $arguments)
    {

        $this->curl->{$function}(...$arguments);

        return $this->fetch();
    }

    /**
     * Fetches the data from response
     * @return $this
     */
    private function fetch()
    {
        if ($this->curl instanceof MultiCurl) {
            $this->curl->complete(function($instance) {
                if ($instance->error) {
                    $this->errors[] = [
                        'code' => $instance->getErrorCode(),
                        'message' => $instance->getErrorMessage()
                    ];
                } else {
                    $this->responseHeaders[] = $instance->getResponseHeaders();
                    $this->responseBody[] = $instance->getResponse();
                }
            });
        } else {
            if ($this->curl->error) {
                $this->errors[] = [
                    'code' => $this->curl->getErrorCode(),
                    'message' => $this->curl->getErrorMessage()
                ];
            } else {
                $this->responseHeaders = $this->curl->getResponseHeaders();
                $this->responseBody = $this->curl->getResponse();
            }
        }

        return $this;
    }

}
