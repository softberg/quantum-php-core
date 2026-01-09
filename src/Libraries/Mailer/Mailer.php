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
 * @since 3.0.0
 */

namespace Quantum\Libraries\Mailer;

use Quantum\Libraries\Mailer\Exceptions\MailerException;
use Quantum\Libraries\Mailer\Contracts\MailerInterface;
use Quantum\App\Exceptions\BaseException;

/**
 * Class Mailer
 * @package Quantum\Libraries\Mailer
 * @method MailerInterface setFrom(string $email, ?string $name = null)
 * @method array getFrom()
 * @method MailerInterface setAddress(string $email, ?string $name = null)
 * @method array getAddresses()
 * @method MailerInterface setSubject(?string $subject)
 * @method string|null getSubject()
 * @method MailerInterface setTemplate(string $templatePath)
 * @method string|null getTemplate()
 * @method MailerInterface setBody($message)
 * @method string|array getBody()
 * @method bool send()
 */
class Mailer
{
    /**
     * SMTP adapter
     */
    public const SMTP = 'smtp';

    /**
     * Mailgun adapter
     */
    public const MAILGUN = 'mailgun';

    /**
     * Mandrill adapter
     */
    public const MANDRILL = 'mandrill';

    /**
     * Sendgrid adapter
     */
    public const SENDGRID = 'sendgrid';

    /**
     * Sendinblue adapter
     */
    public const SENDINBLUE = 'sendinblue';

    /**
     * @var MailerInterface
     */
    private $adapter;

    /**
     * @param MailerInterface $adapter
     */
    public function __construct(MailerInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return MailerInterface
     */
    public function getAdapter(): MailerInterface
    {
        return $this->adapter;
    }

    /**
     * @param string $method
     * @param array|null $arguments
     * @return mixed
     * @throws BaseException
     */
    public function __call(string $method, ?array $arguments)
    {
        if (!method_exists($this->adapter, $method)) {
            throw MailerException::methodNotSupported($method, get_class($this->adapter));
        }

        return $this->adapter->$method(...$arguments);
    }
}
