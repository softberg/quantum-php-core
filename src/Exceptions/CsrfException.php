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
 * Class CsrfException
 * @package Quantum\Exceptions
 */
class CsrfException extends \Exception
{
    /**
     * @return \Quantum\Exceptions\CsrfException
     */
    public static function tokenNotFound(): CsrfException
    {
        return new static(t('csrf_token_not_found'), E_WARNING);
    }

    /**
     * @return \Quantum\Exceptions\CsrfException
     */
    public static function tokenNotMatched(): CsrfException
    {
        return new static(t('csrf_token_not_matched'), E_WARNING);
    }
}
