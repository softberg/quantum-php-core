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
 * @since 2.3.0
 */

namespace Quantum\Exceptions;

/**
 * LangException class
 *
 * @package Quantum
 * @category Exceptions
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
}
