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
 * @since 2.6.0
 */

namespace Quantum\Libraries\Curl;

use Curl\Curl as PhpCurl;
use Curl\MultiCurl;

/**
 * Curl Class
 * @package Quantum\Libraries\Curl
 * @uses php-curl-class/php-curl-class
 */
class Curl
{

    /**
     * Multi curl mode
     */
    const MULTI_CURL = 2;

    /**
     * Curl instance
     * @var \Curl\Curl|\Curl\MultiCurl
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
     * @param int $type
     */
    public function __construct(int $type = 1)
    {
        if ($type == self::MULTI_CURL) {
            $this->curl = new MultiCurl();
        } else {
            $this->curl = new PhpCurl();
        }
    }

    /**
     * Sets the options
     * @param array $options
     * @return \Quantum\Libraries\Curl\Curl
     */
    public function setOptions(array $options): Curl
    {
        $this->curl->setOpts($options);
        return $this;
    }

    /**
     * Gets the option
     * @param int $option
     * @return mixed|null
     */
    public function getOption(int $option)
    {
        return $this->curl->getOpt($option);
    }

    /**
     * Sets the request headers
     * @param array $headers
     * @return \Quantum\Libraries\Curl\Curl
     */
    public function setRequestHeaders(array $headers): Curl
    {
        $this->curl->setHeaders($headers);
        return $this;
    }

    /**
     * Gets single request header or all headers after curl exec
     * @param string|null $header
     * @return mixed|null
     * @throws \Exception
     */
    public function getRequestHeaders(string $header = null)
    {
        if ($this->curl instanceof MultiCurl) {
            throw new \Exception('Method is not available for MultiCurl');
        }

        $requestHeaders = $this->curl->getRequestHeaders();

        if ($header) {
            return $requestHeaders[$header] ?? null;
        }

        return $requestHeaders;
    }

    /**
     * Executes or starts the cURL request
     * @param string|null $url
     * @return \Quantum\Libraries\Curl\Curl
     * @throws \ErrorException
     */
    public function run(string $url = null): Curl
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
     * @return mixed|null
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * Gets the response headers
     * @param string|null $header
     * @return mixed
     */
    public function getResponseHeaders(string $header = null)
    {
        $responseHeaders = [];

        while ($this->responseHeaders->valid()) {
            $responseHeaders[strtolower((string)$this->responseHeaders->key())] = $this->responseHeaders->current();
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
     * @param int|null $option
     * @return mixed
     * @throws \Exception
     */
    public function info(int $option = null)
    {
        if ($this->curl instanceof MultiCurl) {
            throw new \Exception('Method is not available for MultiCurl');
        }

        if ($option) {
            return $this->curl->getInfo($option);
        }

        return $this->curl->getInfo();
    }

    /**
     * Returns the errors
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * __call magic
     * @param string $function
     * @param array $arguments
     * @return \Quantum\Libraries\Curl\Curl
     */
    public function __call(string $function, array $arguments): Curl
    {
        $this->curl->{$function}(...$arguments);
        return $this->fetch();
    }

    /**
     * Fetches the data from response
     * @return \Quantum\Libraries\Curl\Curl
     */
    private function fetch(): Curl
    {
        if ($this->curl instanceof MultiCurl) {
            $this->curl->complete(function ($instance) {
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
