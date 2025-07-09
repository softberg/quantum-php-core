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
 * @since 2.9.8
 */

use Quantum\Config\Exceptions\ConfigException;
use Quantum\View\Exceptions\ViewException;
use League\CommonMark\CommonMarkConverter;
use Quantum\App\Exceptions\BaseException;
use Quantum\View\Factories\ViewFactory;
use Quantum\Di\Exceptions\DiException;
use DebugBar\DebugBarException;
use Quantum\Debugger\Debugger;
use Quantum\View\RawParam;

/**
 * Rendered view
 * @return string|null
 * @throws BaseException
 * @throws ConfigException
 * @throws DebugBarException
 * @throws DiException
 * @throws ReflectionException
 * @throws ViewException
 */
function view(): ?string
{
    return ViewFactory::get()->getView();
}

/**
 * Rendered partial
 * @param string $partial
 * @param array $args
 * @return string|null
 * @throws BaseException
 * @throws ConfigException
 * @throws DebugBarException
 * @throws DiException
 * @throws ReflectionException
 */
function partial(string $partial, array $args = []): ?string
{
    return ViewFactory::get()->renderPartial($partial, $args);
}

/**
 * Gets the param passed to view
 * @param string $key
 * @return mixed|null
 * @throws BaseException
 * @throws ConfigException
 * @throws DebugBarException
 * @throws DiException
 * @throws ReflectionException
 */
function view_param(string $key)
{
    return ViewFactory::get()->getParam($key);
}

/**
 * Creates a raw param
 * @param $value
 * @return RawParam
 */
function raw_param($value): RawParam
{
    return new RawParam($value);
}

/**
 * Rendered debug bar
 * @return string|null
 * @throws DebugBarException
 */
function debugbar(): ?string
{
    $debugger = Debugger::getInstance();

    if ($debugger->isEnabled()) {
        return $debugger->render();
    }

    return null;
}

/**
 * @param string $content
 * @param bool $sanitize
 * @return string
 */
function markdown_to_html(string $content, bool $sanitize = false): string
{
    $converter = new CommonMarkConverter();
    $purifier = null;

    if ($sanitize) {
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
    }

    $html = $converter->convertToHtml($content);

    if ($purifier) {
        return $purifier->purify($html);
    }

    return $html;
}