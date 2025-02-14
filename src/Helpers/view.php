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

use Quantum\Libraries\Config\Exceptions\ConfigException;
use Quantum\Renderer\Exceptions\RendererException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Exceptions\BaseException;
use Quantum\Factory\ViewFactory;
use DebugBar\DebugBarException;
use Quantum\Debugger\Debugger;

/**
 * Rendered view
 * @return string|null
 */
function view(): ?string
{
    return ViewFactory::getInstance()->getView();
}

/**
 * Rendered partial
 * @param string $partial
 * @param array $args
 * @return string|null
 * @throws DiException
 * @throws ReflectionException
 * @throws BaseException
 * @throws ConfigException
 * @throws RendererException
 */
function partial(string $partial, array $args = []): ?string
{
    return ViewFactory::getInstance()->renderPartial($partial, $args);
}

/**
 * Gets the param passed to view
 * @param string $key
 * @return mixed|null
 */
function view_param(string $key)
{
    return ViewFactory::getInstance()->getParam($key);
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