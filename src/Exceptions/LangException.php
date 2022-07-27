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
 * @since 2.8.0
 */

namespace Quantum\Exceptions;

/**
 * Class LangException
 * @package Quantum\Exceptions
 */
class LangException extends \Exception
{
    /**
     * @return \Quantum\Exceptions\LangException
     */
    public static function misconfiguredConfig(): LangException
    {
        return new static(t('misconfigured_lang_config'), E_WARNING);
    }

    /**
     * @param string $name
     * @return \Quantum\Exceptions\LangException
     */
    public static function translationsNotFound(string $name): LangException
    {
        return new static(t('translation_files_not_found', $name), E_WARNING);
    }

    /**
     * @return \Quantum\Exceptions\LangException
     */
    public static function misconfiguredDefaultConfig(): LangException
    {
        return new static(t('misconfigured_lang_default_config'), E_WARNING);
    }
}
