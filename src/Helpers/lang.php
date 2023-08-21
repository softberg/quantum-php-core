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

use Quantum\Exceptions\LangException;
use Quantum\Libraries\Lang\Lang;


/**
 * Gets the current lang
 * @return string|null
 * @throws LangException
 */
function current_lang(): ?string
{
    return Lang::getInstance()->getLang();
}

/**
 * Gets translation
 * @param string $key
 * @param $params
 * @return string|null
 * @throws LangException
 */
function t(string $key, $params = null): ?string
{
    return Lang::getInstance()->getTranslation($key, $params);
}

/**
 * Outputs the translation
 * @param string $key
 * @param mixed $params
 * @throws LangException
 */
function _t(string $key, $params = null)
{
    echo t($key, $params);
}



