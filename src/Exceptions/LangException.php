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

namespace Quantum\Exceptions;

/**
 * Class LangException
 * @package Quantum\Exceptions
 */
class LangException extends \Exception
{
    /**
     * Misconfigured lang config
     */
    const MISCONFIGURED_LANG_CONFIG = 'Misconfigured lang config';

    /**
     * Translations not found
     */
    const TRANSLATION_FILES_NOT_FOUND = 'Translations for language `{%1}` not found';

    /**
     * Misconfigured default lang config
     */
    const MISCONFIGURED_LANG_DEFAULT_CONFIG = 'Misconfigured default lang config';

    /**
     * @return \Quantum\Exceptions\LangException
     */
    public static function misconfiguredConfig(): LangException
    {
        return new static(self::MISCONFIGURED_LANG_CONFIG, E_WARNING);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\LangException
     */
    public static function translationsNotFound(string $name): LangException
    {
        return new static(_message(self::TRANSLATION_FILES_NOT_FOUND, $name), E_WARNING);
    }

    /**
     * @return \Quantum\Exceptions\LangException
     */
    public static function misconfiguredDefaultConfig(): LangException
    {
        return new static(self::MISCONFIGURED_LANG_DEFAULT_CONFIG, E_WARNING);
    }
}
