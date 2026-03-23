<?php

declare(strict_types=1);

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Mailer\Enums;

/**
 * Class MailerType
 * @package Quantum\Mailer
 * @codeCoverageIgnore
 */
final class MailerType
{
    public const SMTP = 'smtp';

    public const MAILGUN = 'mailgun';

    public const MANDRILL = 'mandrill';

    public const SENDGRID = 'sendgrid';

    public const SENDINBLUE = 'sendinblue';

    private function __construct()
    {
    }
}
