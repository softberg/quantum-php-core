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
 * @since 2.5.0
 */

use Quantum\Http\Request;
use Quantum\Http\Response;

/**
 * Gets the base url
 * @return string
 */
function base_url(): string
{
    return config()->get('base_url') ?? Request::getProtocol() . '://' . Request::getHost() . (Request::getPort() ? ':' . Request::getPort() : '');
}

/**
 * Gets the current url
 * @return string
 */
function current_url(): string
{
    return Request::getProtocol() . '://' . Request::getHost() . (Request::getPort() ? ':' . Request::getPort() : '') . '/' . Request::getUri() . (Request::getQuery() ? '?' . Request::getQuery() : '');
}

/**
 * Redirect
 * @param string $url
 * @param integer|null $code
 */
function redirect(string $url, int $code = null)
{
    Response::redirect($url, $code);
}

/**
 * Redirect with data
 * @param string $url
 * @param array $data
 * @param int|null $code
 * @throws \Quantum\Exceptions\CryptorException
 * @throws \Quantum\Exceptions\DatabaseException
 * @throws \Quantum\Exceptions\DiException
 * @throws \Quantum\Exceptions\LoaderException
 * @throws \Quantum\Exceptions\ModelException
 * @throws \Quantum\Exceptions\SessionException
 * @throws \ReflectionException
 */
function redirectWith(string $url, array $data, int $code = null)
{
    session()->set('__prev_request', $data);
    Response::redirect($url, $code);
}

/**
 * Keeps old input values after redirect
 * @param string $key
 * @return mixed|null
 * @throws \Quantum\Exceptions\CryptorException
 * @throws \Quantum\Exceptions\DatabaseException
 * @throws \Quantum\Exceptions\DiException
 * @throws \Quantum\Exceptions\LoaderException
 * @throws \Quantum\Exceptions\ModelException
 * @throws \Quantum\Exceptions\SessionException
 * @throws \ReflectionException
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

/**
 * Slugify the string
 * @param string $text
 * @return string
 */
function slugify(string $text): string
{
    $text = trim($text, ' ');
    $text = preg_replace('/[^\p{L}\p{N}]/u', ' ', $text);
    $text = preg_replace('/\s+/', '-', $text);
    $text = trim($text, '-');
    $text = mb_strtolower($text);
    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}

/**
 * Gets the assets url
 * @return string
 */
function asset_url(): string
{
    return base_url() . '/assets';
}


