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

namespace Quantum\Libraries\HttpClient;

use Quantum\Libraries\HttpClient\Exceptions\HttpClientException;
use Quantum\Exceptions\BaseException;
use Curl\CaseInsensitiveArray;
use ErrorException;
use Curl\MultiCurl;
use Curl\Curl;

/**
 * HttpClient Class
 * @package Quantum\Libraries\HttpClient
 * @uses php-curl-class/php-curl-class
 * @method object addGet(string $url, array $data = [])
 * @method object addPost(string $url, string $data = '', bool $follow_303_with_post = false)
 * @method setHeader($key, $value)
 * @method setHeaders($headers)
 * @method setOpt($option, $value)
 */
class HttpClient
{

    /**
     * Available methods
     */
    const METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Response headers section
     */
    const RESPONSE_HEADERS = 'headers';

    /**
     * Response cookies section
     */
    const RESPONSE_COOKIES = 'cookies';

    /**
     * Response body section
     */
    const RESPONSE_BODY = 'body';

    /**
     * @var MultiCurl|Curl
     */
    private $client = null;

    /**
     * @var string
     */
    private $method = 'GET';

    /**
     * @var mixed|null
     */
    private $data = null;

    /**
     * @var array
     */
    private $response = [];

    /**
     * @var array
     */
    private $errors = [];

    /**
     * Creates request
     * @param string $url
     * @return HttpClient
     */
    public function createRequest(string $url): HttpClient
    {
        $this->client = new Curl();
        $this->client->setUrl($url);
        return $this;
    }

    /**
     * Creates multi request
     * @return HttpClient
     */
    public function createMultiRequest(): HttpClient
    {
        $this->client = new MultiCurl();

        $this->client->complete(function (Curl $instance) {
            $this->handleResponse($instance);
        });

        return $this;
    }

    /**
     * Creates async multi request
     * @param callable $success
     * @param callable $error
     * @return HttpClient
     */
    public function createAsyncMultiRequest(callable $success, callable $error): HttpClient
    {
        $this->client = new MultiCurl();

        $this->client->success($success);
        $this->client->error($error);

        return $this;
    }

    /**
     * Sets http method
     * @param string $method
     * @return $this
     * @throws HttpClientException
     */
    public function setMethod(string $method): HttpClient
    {
        if (!in_array($method, self::METHODS)) {
            throw HttpClientException::methodNotAvailable($method);
        }

        $this->method = $method;
        return $this;
    }

    /**
     * Gets the current http method
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Sets data
     * @param mixed $data
     * @return HttpClient
     */
    public function setData($data): HttpClient
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Gets the data
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Checks if the request is multi cURL
     * @return bool
     */
    public function isMultiRequest(): bool
    {
        return $this->client instanceof MultiCurl;
    }

    /**
     * Starts the request
     * @throws ErrorException
     * @throws HttpClientException
     */
    public function start(): HttpClient
    {
        if (!$this->client) {
            throw HttpClientException::requestNotCreated();
        }

        if ($this->isMultiRequest()) {
            $this->client->start();
        } else {
            $this->startSingleRequest();
        }

        return $this;
    }

    /**
     * Gets single or all request headers
     * @param string|null $header
     * @return mixed|null
     * @throws BaseException
     */
    public function getRequestHeaders(string $header = null)
    {
        $this->ensureSingleRequest();

        $requestHeaders = $this->formatHeaders($this->client->getRequestHeaders());

        if ($header) {
            return $requestHeaders[$header] ?? null;
        }

        return $requestHeaders;
    }

    /**
     * Gets the response headers
     * @param string|null $header
     * @return mixed|null
     * @throws BaseException
     */
    public function getResponseHeaders(string $header = null)
    {
        $this->ensureSingleRequest();

        $responseHeaders = $this->getResponse()[self::RESPONSE_HEADERS];

        if ($header) {
            return $responseHeaders[$header] ?? null;
        }

        return $responseHeaders;
    }

    /**
     * Gets the response cookies
     * @param string|null $cookie
     * @return mixed|null
     * @throws BaseException
     */
    public function getResponseCookies(string $cookie = null)
    {
        $this->ensureSingleRequest();

        $responseCookies = $this->getResponse()[self::RESPONSE_COOKIES];

        if ($cookie) {
            return $responseCookies[$cookie] ?? null;
        }

        return $responseCookies;
    }

    /**
     * Gets the response body
     * @return mixed|null
     * @throws BaseException
     */
    public function getResponseBody()
    {
        $this->ensureSingleRequest();

        return $this->response[$this->client->getId()][self::RESPONSE_BODY] ?? null;
    }

    /**
     * Gets the entire response
     * @return array
     */
    public function getResponse(): array
    {
        return $this->isMultiRequest() ? $this->response : ($this->response[$this->client->getId()] ?? []);
    }

    /**
     * Returns the errors
     * @return array
     */
    public function getErrors(): array
    {
        return $this->isMultiRequest() ? $this->errors : ($this->errors[$this->client->getId()] ?? []);
    }

    /**
     * Gets the curl info
     * @param int|null $option
     * @return mixed
     * @throws BaseException
     */
    public function info(int $option = null)
    {
        $this->ensureSingleRequest();

        return $option ? $this->client->getInfo($option) : $this->client->getInfo();
    }

    /**
     * Gets the current url being executed
     * @return string|null
     * @throws BaseException
     */
    public function url(): ?string
    {
        $this->ensureSingleRequest();

        return $this->client->getUrl();
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return $this
     * @throws BaseException
     * @throws HttpClientException
     */
    public function __call(string $method, array $arguments): HttpClient
    {
        if (is_null($this->client)) {
            throw HttpClientException::requestNotCreated();
        }

        if (!method_exists($this->client, $method)) {
            throw HttpClientException::methodNotSupported($method, get_class($this->client));
        }

        $this->client->$method(...$arguments);

        return $this;
    }

    /**
     * @return void
     * @throws ErrorException
     */
    private function startSingleRequest(): void
    {
        $this->client->setOpt(CURLOPT_CUSTOMREQUEST, $this->method);

        if ($this->data) {
            $this->client->setOpt(CURLOPT_POSTFIELDS, $this->client->buildPostData($this->data));
        }

        $this->client->exec();
        $this->handleResponse($this->client);
    }

    /**
     * Handles the response
     * @param Curl $instance
     */
    private function handleResponse(Curl $instance)
    {
        if ($instance->isError()) {
            $this->errors[$instance->getId()] = [
                'code' => $instance->getErrorCode(),
                'message' => $instance->getErrorMessage()
            ];
        }

        $this->response[$instance->getId()] = [
            self::RESPONSE_HEADERS => $this->formatHeaders($instance->getResponseHeaders()),
            self::RESPONSE_COOKIES => $instance->getResponseCookies(),
            self::RESPONSE_BODY => $instance->getResponse()
        ];
    }

    private function formatHeaders(CaseInsensitiveArray $headers): array
    {
        $formatted = [];

        foreach ($headers as $key => $value) {
            $formatted[strtolower($key)] = $value;
        }

        return $formatted;
    }

    /**
     * @return void
     * @throws BaseException
     */
    private function ensureSingleRequest(): void
    {
        if ($this->isMultiRequest()) {
            throw HttpClientException::methodNotSupported(__METHOD__, MultiCurl::class);
        }
    }
}