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
 * @since 3.0.0
 */

use Quantum\Config\Exceptions\ConfigException;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\App\Enums\ReservedKeys;
use Quantum\Http\Enums\ContentType;
use Quantum\Http\Enums\StatusCode;
use DebugBar\DebugBarException;
use Quantum\Http\Response;
use Quantum\Http\Request;

/**
 * Gets the base url
 * @param bool $withModulePrefix
 * @return string
 */
function base_url(bool $withModulePrefix = false): string
{
    return Request::getBaseUrl($withModulePrefix);
}

/**
 * Gets the current url
 * @return string
 */
function current_url(): string
{
    return Request::getCurrentUrl();
}

/**
 * Redirect
 * @param string $url
 * @param int $code
 */
function redirect(string $url, int $code = StatusCode::FOUND)
{
    Response::redirect($url, $code);
}

/**
 * Redirect with data
 * @param string $url
 * @param array $data
 * @param int $code
 * @return void
 * @throws BaseException
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 */
function redirectWith(string $url, array $data, int $code = StatusCode::FOUND)
{
    session()->set(ReservedKeys::PREV_REQUEST, $data);
    Response::redirect($url, $code);
}

/**
 * Gets old input values after redirect
 * @param string $key
 * @return mixed|null
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 * @throws BaseException
 */
function old(string $key)
{
    if (!session()->has(ReservedKeys::PREV_REQUEST)) {
        return null;
    }

    $prevRequest = session()->get(ReservedKeys::PREV_REQUEST);

    if (!is_array($prevRequest) || !isset($prevRequest[$key])) {
        return null;
    }

    $value = $prevRequest[$key];
    unset($prevRequest[$key]);

    session()->set(ReservedKeys::PREV_REQUEST, $prevRequest);

    return $value;
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
 * Handles page not found
 * @return void
 * @throws BaseException
 * @throws ConfigException
 * @throws DiException
 * @throws ReflectionException
 * @throws DebugBarException
 */
function page_not_found()
{
    $acceptHeader = Response::getHeader('Accept');

    $isJson = $acceptHeader === ContentType::JSON;

    if ($isJson) {
        Response::json(
            ['status' => 'error', 'message' => 'Page not found',],
            StatusCode::NOT_FOUND
        );
    } else {
        Response::html(
            partial('errors' . DS . StatusCode::NOT_FOUND),
            StatusCode::NOT_FOUND
        );
    }
}
