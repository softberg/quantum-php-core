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
 * @since 2.9.9
 */

namespace Quantum\Libraries\Lang\Exceptions;

use Quantum\App\Exceptions\BaseException;

/**
 * Class LangException
 * @package Quantum\Libraries\Lang
 */
class LangException extends BaseException
{

    /**
     * @return LangException
     */
    public static function translationsNotFound(): LangException
    {
        return new static('Translation files not found', E_WARNING);
    }

    /**
     * @return LangException
     */
    public static function misconfiguredDefaultConfig(): LangException
    {
        return new static('Misconfigured lang default config', E_WARNING);
    }
}