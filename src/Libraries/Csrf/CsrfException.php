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

namespace Quantum\Libraries\Csrf;

use Quantum\Exceptions\AppException;

/**
 * Class CsrfException
 * @package Quantum\Libraries\Csrf
 */
class CsrfException extends AppException
{
    /**
     * @return CsrfException
     */
    public static function tokenNotFound(): CsrfException
    {
        return new static(t('exception.csrf_token_not_found'), E_WARNING);
    }

    /**
     * @return CsrfException
     */
    public static function tokenNotMatched(): CsrfException
    {
        return new static(t('exception.csrf_token_not_matched'), E_WARNING);
    }
}
