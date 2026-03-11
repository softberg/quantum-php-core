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
use Quantum\Lang\Exceptions\LangException;
use Quantum\Lang\Factories\LangFactory;
use Quantum\Di\Exceptions\DiException;

/**
 * Gets the current lang
 * @throws ConfigException
 * @throws DiException
 * @throws LangException
 * @throws ReflectionException
 */
function current_lang(): ?string
{
    return LangFactory::get()->getLang();
}

/**
 * Gets translation
 * @param $params
 * @throws ConfigException
 * @throws ReflectionException
 * @throws DiException
 * @throws LangException
 */
function t(string $key, $params = null): ?string
{
    return LangFactory::get()->getTranslation($key, $params);
}

/**
 * Outputs the translation
 * @param $params
 * @throws ConfigException
 * @throws DiException
 * @throws LangException
 * @throws ReflectionException
 */
function _t(string $key, $params = null): void
{
    echo t($key, $params);
}
