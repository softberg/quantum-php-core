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

namespace Quantum\Libraries\Mailer;


/**
 * Mailer Abstract Layer interface
 * @package Quantum\Libraries\Mailer
 */
interface MailerInterface
{
        
    /**
     * Send mail by using Sendinblue
     * @param  string $data
     * @return bool
     */
    public function sendMail(string $data);
}
