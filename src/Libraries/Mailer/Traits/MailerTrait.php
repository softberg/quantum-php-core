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

namespace Quantum\Libraries\Mailer\Traits;

use Quantum\Libraries\Mailer\Contracts\MailerInterface;
use Quantum\Libraries\HttpClient\HttpClient;
use Quantum\Di\Exceptions\DiException;
use Quantum\Libraries\Mailer\MailTrap;
use PHPMailer\PHPMailer\PHPMailer;
use Quantum\Debugger\Debugger;
use ReflectionException;
use Exception;

/**
 * trait MailerTrait
 * @package Quantum\Libraries\Mailer
 */
trait MailerTrait
{

    /**
     * From address and name
     * @var array
     */
    private $from = [];

    /**
     * To addresses
     * @var array
     */
    private $addresses = [];

    /**
     * Email subject
     * @var string
     */
    private $subject = null;

    /**
     * Email body
     * @var string|array
     */
    private $message = null;

    /**
     * @var string
     */
    private static $messageId = null;

    /**
     * Template path
     * @var string
     */
    private $templatePath = null;

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var PHPMailer
     */
    protected $mailer;

    /**
     * Reply To addresses
     * @var array
     */
    protected $replyToAddresses = [];

    /**
     * CC addresses
     * @var array
     */
    protected $ccAddresses = [];

    /**
     * BCC addresses
     * @var array
     */
    protected $bccAddresses = [];

    /**
     * Email attachments
     * @var array
     */
    protected $attachments = [];

    /**
     * Email attachments created from string
     * @var array
     */
    protected $stringAttachments = [];

    /**
     * Sets the 'From' email and the name
     * @param string $email
     * @param string|null $name
     * @return MailerInterface
     */
    public function setFrom(string $email, ?string $name = null): MailerInterface
    {
        $this->from['email'] = $email;
        $this->from['name'] = $name;
        return $this;
    }

    /**
     * Gets 'From' email and the "name"
     * @return array
     */
    public function getFrom(): array
    {
        return $this->from;
    }

    /**
     * Sets 'To' addresses
     * @param string $email
     * @param string|null $name
     * @return MailerInterface
     */
    public function setAddress(string $email, ?string $name = null): MailerInterface
    {
        $this->addresses[] = [
            'email' => $email,
            'name' => $name
        ];

        return $this;
    }

    /**
     * Gets 'To' addresses
     * @return array
     */
    public function getAddresses(): array
    {
        return $this->addresses;
    }

    /**
     * Sets the subject
     * @param string|null $subject
     * @return MailerInterface
     */
    public function setSubject(?string $subject): MailerInterface
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Gets the subject
     * @return string
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * Sets the template
     * @param string $templatePath
     * @return MailerInterface
     */
    public function setTemplate(string $templatePath): MailerInterface
    {
        $this->templatePath = $templatePath;
        return $this;
    }

    /**
     * Gets the template
     * @return string|null
     */
    public function getTemplate(): ?string
    {
        return $this->templatePath;
    }

    /**
     * Sets the body
     * @param string|array $message
     * @return MailerInterface
     */
    public function setBody($message): MailerInterface
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Gets the body
     * @return array|string|null
     */
    public function getBody()
    {
        return $this->message;
    }

    /**
     * @inheritDoc
     * @throws DiException
     * @throws ReflectionException
     * @throws Exception
     */
    public function send(): bool
    {
        $this->prepare();

        $sent = config()->get('mailer.mail_trap') ? $this->saveEmail() : $this->sendEmail();

        $this->resetFields();

        if ($this->name !== 'SMTP' && !$sent) {
            warning($this->httpClient->getErrors(), ['tab' => Debugger::MAILS]);
        }

        return $sent;
    }

    /**
     * Gets the message ID
     * @return string|null
     * @throws Exception
     */
    public function getMessageId(): ?string
    {
        if (self::$messageId) {
            return self::$messageId;
        }

        if ($this->name == 'SMTP') {
            preg_match('/<(.*?)@/', preg_quote($this->mailer->getLastMessageID()), $matches);
            self::$messageId = $matches[1] ?? null;
        } else {
            self::$messageId = bin2hex(random_bytes(16));
        }

        return self::$messageId;
    }

    /**
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws Exception
     */
    private function saveEmail(): bool
    {
        if ($this->name == 'SMTP') {
            $this->mailer->preSend();
        }

        return MailTrap::getInstance()->saveMessage($this->getMessageId(), $this->getMessageContent());
    }

    /**
     * Create message body from email template
     * @return string
     */
    private function createFromTemplate(): string
    {
        ob_start();
        ob_implicit_flush(0);

        if (is_array($this->message)) {
            extract($this->message, EXTR_OVERWRITE);
        }

        require $this->templatePath . '.php';

        return ob_get_clean();
    }

    /**
     * Gets the complete message
     * @return string
     * @throws Exception
     */
    private function getMessageContent(): string
    {
        if ($this->name == 'SMTP') {
            return $this->mailer->getSentMIMEMessage();
        }

        return $this->generateMessage();
    }

    /**
     * Generates the message content
     * @return string
     * @throws Exception
     */
    private function generateMessage(): string
    {
        $message = 'Date: ' . date('D, j M Y H:i:s O') . PHP_EOL;

        $message .= 'To: ';

        foreach ($this->addresses as $address) {
            $message .= $address['name'] . ' <' . $address['email'] . '>' . PHP_EOL;
        }

        $message .= 'From: ' . $this->from['name'] . ' <' . $this->from['email'] . '>' . PHP_EOL;

        $message .= 'Subject: ' . $this->subject . PHP_EOL;

        $message .= 'Message-ID: <' . $this->getMessageId() . '@' . base_url() . '>' . PHP_EOL;

        $message .= 'X-Mailer: ' . $this->name . PHP_EOL;

        $message .= 'MIME-Version: 1.0' . PHP_EOL;

        $message .= 'Content-Type: text/html; charset=UTF-8' . PHP_EOL . PHP_EOL;

        $message .= $this->message . PHP_EOL;

        return $message;
    }

    /**
     * Resets the fields
     */
    private function resetFields()
    {
        $this->from = [];
        $this->addresses = [];
        $this->subject = null;
        $this->message = null;
        $this->templatePath = null;

        if ($this->name == 'SMTP') {
            $this->replyToAddresses = [];
            $this->ccAddresses = [];
            $this->bccAddresses = [];
            $this->attachments = [];
            $this->stringAttachments = [];

            $this->mailer->clearAddresses();
            $this->mailer->clearCCs();
            $this->mailer->clearBCCs();
            $this->mailer->clearReplyTos();
            $this->mailer->clearAllRecipients();
            $this->mailer->clearAttachments();
            $this->mailer->clearCustomHeaders();
        }
    }
}