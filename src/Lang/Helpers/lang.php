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
use Quantum\Loader\Exceptions\LoaderException;
use Quantum\Lang\Exceptions\LangException;
use Quantum\Lang\Factories\LangFactory;
use Quantum\Di\Exceptions\DiException;

/**
 * Gets the current lang
 * @throws LangException|LoaderException|ConfigException|DiException|ReflectionException
 */
function current_lang(): ?string
{
    return LangFactory::get()->getLang();
}

/**
 * Gets translation
 * @param array<int|string, mixed>|string|null $params
 * @throws LangException|LoaderException|ConfigException|DiException|ReflectionException
 */
function t(string $key, $params = null): ?string
{
    return LangFactory::get()->getTranslation($key, $params);
}

/**
 * Outputs the translation
 * @param array<int|string, mixed>|string|null $params
 * @throws LangException|LoaderException|ConfigException|DiException|ReflectionException
 */
function _t(string $key, $params = null): void
{
    echo t($key, $params);
}
