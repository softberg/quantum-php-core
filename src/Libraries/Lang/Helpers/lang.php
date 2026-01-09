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

use Quantum\Libraries\Lang\Exceptions\LangException;
use Quantum\Libraries\Lang\Factories\LangFactory;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Di\Exceptions\DiException;

/**
 * Gets the current lang
 * @return string|null
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
 * @param string $key
 * @param $params
 * @return string|null
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
 * @param string $key
 * @param $params
 * @return void
 * @throws ConfigException
 * @throws DiException
 * @throws LangException
 * @throws ReflectionException
 */
function _t(string $key, $params = null)
{
    echo t($key, $params);
}
