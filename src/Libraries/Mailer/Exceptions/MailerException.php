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
 * @since 2.9.7
 */

namespace Quantum\Libraries\Mailer\Exceptions;

use Quantum\App\Exceptions\BaseException;

/**
 * Class MailerException
 * @package Quantum\Libraries\Mailer
 */
class MailerException extends BaseException
{

    /**
     * @param string $error
     * @return MailerException
     */
    public static function unableToSend(string $error): MailerException
    {
        return new static($error, E_WARNING);
    }
}