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

namespace Quantum\Libraries\Lang;

use Quantum\Exceptions\AppException;

/**
 * Class LangException
 * @package Quantum\Libraries\Lang
 */
class LangException extends AppException
{
    /**
     * @return LangException
     */
    public static function misconfiguredConfig(): LangException
    {
        return new static(t('exception.misconfigured_lang_config'), E_WARNING);
    }

    /**
     * @param string $name
     * @return LangException
     */
    public static function translationsNotFound(string $name): LangException
    {
        return new static(t('exception.translation_files_not_found', $name), E_WARNING);
    }

    /**
     * @return LangException
     */
    public static function misconfiguredDefaultConfig(): LangException
    {
        return new static(t('exception.misconfigured_lang_default_config'), E_WARNING);
    }
}
