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
use Quantum\Http\Request;
use Quantum\Http\Response;

if (!function_exists('base_url')) {

    /**
     * Gets the base url
     * @return string
     */
    function base_url()
    {
        return config()->get('base_url') ?? Request::getProtocol() . '://' . Request::getHost() . (Request::getPort() ? ':' . Request::getPort() : '');
    }

}

if (!function_exists('current_url')) {

    /**
     * Gets the current url
     * @return string
     */
    function current_url()
    {
        return Request::getProtocol() . '://' . Request::getHost() . (Request::getPort() ? ':' . Request::getPort() : '') . '/' . Request::getUri() . (Request::getQuery() ? '?' . Request::getQuery() : '');
    }

}

if (!function_exists('redirect')) {

    /**
     * Redirect
     * @param string $url
     * @param integer $code
     */
    function redirect($url, $code = null)
    {
        Response::redirect($url, $code);
    }

}

if (!function_exists('redirectWith')) {

    /**
     * Redirect with
     * @param string $url
     * @param array $data
     * @param integer $code
     */
    function redirectWith($url, $data, $code = null)
    {
        session()->set('__prev_request', $data);
        Response::redirect($url, $code);
    }

}

if (!function_exists('old')) {

    /**
     * Keeps old input values after redirect
     * @param string $key
     */
    function old($key)
    {
        if (session()->has('__prev_request')) {
            $prevRequest = session()->get('__prev_request');

            if (array_key_exists($key, $prevRequest)) {
                $value = $prevRequest[$key];
                unset($prevRequest[$key]);
                session()->set('__prev_request', $prevRequest);
                return $value;
            }
        }

        return null;
    }

}

if (!function_exists('get_referrer')) {

    /**
     * Gets the referrer
     * @return string|null
     */
    function get_referrer()
    {
        return Request::getReferrer();
    }

}

if (!function_exists('slugify')) {

    /**
     * Slugify the string
     * @param string $text
     * @return string
     */
    function slugify($text)
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

}

if (!function_exists('asset_url')) {

    /**
     * Gets the assets url
     * @return string
     */
    function asset_url()
    {
        return base_url() . '/assets';
    }

}

