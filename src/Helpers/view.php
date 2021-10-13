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

use Quantum\Debugger\Debugger;
use Quantum\Factory\ViewFactory;


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
 * @throws \Quantum\Exceptions\DiException
 * @throws \Quantum\Exceptions\HookException
 * @throws \Quantum\Exceptions\ViewException
 * @throws \ReflectionException
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
 */
function debugbar(): ?string
{
    if (filter_var(config()->get('debug'), FILTER_VALIDATE_BOOLEAN)) {
        return (new Debugger())->render();
    }

    return null;
}
