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

namespace Quantum\Libraries\Storage\Traits;

use Quantum\Http\Exceptions\HttpException;
use Quantum\Exceptions\BaseException;
use Exception;

/**
 * Trait CloudAppTrait
 * @package Quantum\Libraries\Storage
 */
trait CloudAppTrait
{

    /**
     * @inheritDoc
     * @throws BaseException
     * @throws HttpException
     * @throws Exception
     */
    public function sendRequest(string $uri, $data = null, array $headers = [], string $method = 'POST')
    {
        $this->httpClient
            ->createRequest($uri)
            ->setMethod($method)
            ->setData($data)
            ->setHeaders($headers)
            ->start();

        $errors = $this->httpClient->getErrors();
        $responseBody = $this->httpClient->getResponseBody();

        if ($errors) {
            $code = $errors['code'];

            if ($this->accessTokenNeedsRefresh($code, $responseBody)) {
                $prevUrl = $this->httpClient->url();
                $prevData = $this->httpClient->getData();
                $prevHeaders = $this->httpClient->getRequestHeaders();

                $refreshToken = $this->tokenService->getRefreshToken();

                $accessToken = $this->fetchAccessTokenByRefreshToken($refreshToken);

                $prevHeaders['Authorization'] = 'Bearer ' . $accessToken;

                $responseBody = $this->sendRequest($prevUrl, $prevData, $prevHeaders);

            } else {
                throw new Exception(json_encode($responseBody ?? $errors), E_ERROR);
            }
        }

        return $responseBody;
    }
}