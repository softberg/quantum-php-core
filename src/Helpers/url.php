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

use Quantum\Exceptions\DatabaseException;
use Quantum\Exceptions\SessionException;
use Quantum\Exceptions\CryptorException;
use Quantum\Exceptions\ConfigException;
use Quantum\Exceptions\LangException;
use Quantum\Exceptions\DiException;
use Quantum\Http\Request;
use Quantum\Http\Response;

/**
 * Gets the base url
 * @param bool $withModulePrefix
 * @return string
 */
function base_url(bool $withModulePrefix = false): string
{
    $baseUrl = config()->get('base_url');

    if ($baseUrl) {
        return $baseUrl;
    }

    $protocol = Request::getProtocol();
    $host = Request::getHost();
    $port = Request::getPort();

    $portPart = ($port && $port != 80) ? ':' . $port : '';

    $modulePrefix = ($withModulePrefix && !empty(route_prefix())) ? '/' . route_prefix() : '';

    return $protocol . '://' . $host . $portPart . $modulePrefix;
}

/**
 * Gets the current url
 * @return string
 */
function current_url(): string
{
    $protocol = Request::getProtocol();
    $host = Request::getHost();
    $port = Request::getPort();

    $portPart = ($port && $port != 80) ? ':' . $port : '';

    $uri = Request::getUri();
    $query = Request::getQuery();

    $queryPart = $query ? '?' . $query : '';

    return $protocol . '://' . $host . $portPart . '/' . $uri . $queryPart;
}

/**
 * Redirect
 * @param string $url
 * @param int $code
 */
function redirect(string $url, int $code = 302)
{
    Response::redirect($url, $code);
}

/**
 * Redirect with data
 * @param string $url
 * @param array $data
 * @param int $code
 * @throws ReflectionException
 * @throws ConfigException
 * @throws CryptorException
 * @throws DatabaseException
 * @throws DiException
 * @throws SessionException
 * @throws LangException
 */
function redirectWith(string $url, array $data, int $code = 302)
{
    session()->set('__prev_request', $data);
    Response::redirect($url, $code);
}

/**
 * Gets old input values after redirect
 * @param string $key
 * @return mixed|null
 * @throws ReflectionException
 * @throws ConfigException
 * @throws CryptorException
 * @throws DatabaseException
 * @throws DiException
 * @throws SessionException
 * @throws LangException
 */
function old(string $key)
{
    if (session()->has('__prev_request')) {
        $prevRequest = session()->get('__prev_request');

        if (is_array($prevRequest) && array_key_exists($key, $prevRequest)) {
            $value = $prevRequest[$key];
            unset($prevRequest[$key]);
            session()->set('__prev_request', $prevRequest);
            return $value;
        }
    }

    return null;
}

/**
 * Gets the referrer
 * @return string|null
 */
function get_referrer(): ?string
{
    return Request::getReferrer();
}


