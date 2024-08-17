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

namespace Quantum\Libraries\Curl;

use Quantum\Exceptions\HttpException;
use Quantum\Exceptions\LangException;
use Quantum\Exceptions\AppException;
use Curl\CaseInsensitiveArray;
use ErrorException;
use Curl\MultiCurl;
use Curl\Curl;

/**
 * Curl Class
 * @package Quantum\Libraries\Curl
 * @uses php-curl-class/php-curl-class
 * @method object addGet(string $url, array $data = [])
 * @method object addPost(string $url, string $data = '', bool $follow_303_with_post = false)
 * @method setHeaders($headers)
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
     * @return HttpClient
     * @throws HttpException
     * @throws LangException
     */
    public function setMethod(string $method): HttpClient
    {
        if (!in_array($method, self::METHODS)) {
            throw HttpException::methodNotAvailable($method);
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
     * @throws HttpException
     * @throws LangException
     */
    public function start(): HttpClient
    {
        if (!$this->client) {
            throw HttpException::requestNotCreated();
        }

        if ($this->isMultiRequest()) {
            $this->client->start();
            return $this;
        }

        $this->client->setOpt(CURLOPT_CUSTOMREQUEST, $this->method);

        if ($this->data) {
            $this->client->setOpt(CURLOPT_POSTFIELDS, $this->client->buildPostData($this->data));
        }

        $this->client->exec();

        $this->handleResponse($this->client);

        return $this;
    }

    /**
     * Gets single or all request headers
     * @param string|null $header
     * @return mixed|null
     * @throws AppException
     */
    public function getRequestHeaders(string $header = null)
    {
        if ($this->isMultiRequest()) {
            throw AppException::methodNotSupported(__METHOD__, MultiCurl::class);
        }

        $requestHeaders = $this->handleHeaders($this->client->getRequestHeaders());

        if ($header) {
            return $requestHeaders[$header] ?? null;
        }

        return $requestHeaders;
    }

    /**
     * Gets the response headers
     * @param string|null $header
     * @return mixed|null
     * @throws AppException
     */
    public function getResponseHeaders(string $header = null)
    {
        if ($this->isMultiRequest()) {
            throw AppException::methodNotSupported(__METHOD__, MultiCurl::class);
        }

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
     * @throws AppException
     */
    public function getResponseCookies(string $cookie = null)
    {
        if ($this->isMultiRequest()) {
            throw AppException::methodNotSupported(__METHOD__, MultiCurl::class);
        }

        $responseCookies = $this->getResponse()[self::RESPONSE_COOKIES];

        if ($cookie) {
            return $responseCookies[$cookie] ?? null;
        }

        return $responseCookies;
    }

    /**
     * Gets the response body
     * @return mixed|null
     * @throws AppException
     */
    public function getResponseBody()
    {
        if ($this->isMultiRequest()) {
            throw AppException::methodNotSupported(__METHOD__, MultiCurl::class);
        }

        return $this->response[$this->client->getId()][self::RESPONSE_BODY] ?? null;
    }

    /**
     * Gets the entire response
     * @return array
     */
    public function getResponse(): array
    {
        if (!$this->isMultiRequest()) {
            return $this->response[$this->client->getId()] ?? [];
        }

        return $this->response;
    }

    /**
     * Returns the errors
     * @return array
     */
    public function getErrors(): array
    {
        if (!$this->isMultiRequest()) {
            return $this->errors[$this->client->getId()] ?? [];
        }

        return $this->errors;
    }

    /**
     * Gets the curl info
     * @param int|null $option
     * @return mixed
     * @throws AppException
     */
    public function info(int $option = null)
    {
        if ($this->isMultiRequest()) {
            throw AppException::methodNotSupported(__METHOD__, MultiCurl::class);
        }

        if ($option) {
            return $this->client->getInfo($option);
        }

        return $this->client->getInfo();
    }

    /**
     * Gets the current url being executed
     * @return string|null
     * @throws AppException
     */
    public function url(): ?string
    {
        if ($this->isMultiRequest()) {
            throw AppException::methodNotSupported(__METHOD__, MultiCurl::class);
        }

        return $this->client->getUrl();
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return HttpClient
     * @throws HttpException
     * @throws LangException
     * @throws AppException
     */
    public function __call(string $method, array $arguments): HttpClient
    {
        if (is_null($this->client)) {
            throw HttpException::requestNotCreated();
        }

        if (!method_exists($this->client, $method)) {
            throw AppException::methodNotSupported($method, get_class($this->client));
        }

        $this->client->$method(...$arguments);

        return $this;
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
            self::RESPONSE_HEADERS => $this->handleHeaders($instance->getResponseHeaders()),
            self::RESPONSE_COOKIES => $instance->getResponseCookies(),
            self::RESPONSE_BODY => $instance->getResponse()
        ];
    }

    /**
     * @param CaseInsensitiveArray $httpHeaders
     * @return array
     */
    private function handleHeaders(CaseInsensitiveArray $httpHeaders): array
    {
        $headers = [];

        while ($httpHeaders->valid()) {
            $headers[strtolower((string)$httpHeaders->key())] = $httpHeaders->current();
            $httpHeaders->next();
        }

        $httpHeaders->rewind();

        return $headers;
    }

}
