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

namespace Quantum\Libraries\Mailer\Adapters;

use Quantum\Libraries\Mailer\Contracts\MailerInterface;
use Quantum\Libraries\Mailer\Traits\MailerTrait;
use PHPMailer\PHPMailer\PHPMailer;
use Quantum\Debugger\Debugger;
use PHPMailer\PHPMailer\SMTP;
use Exception;

/**
 * class SmtpAdapter
 * @package Quantum\Libraries\Mailer
 */
class SmtpAdapter implements MailerInterface
{

    use MailerTrait;

    /**
     * @var string
     */
    public $name = 'SMTP';

    /**
     * @var PHPMailer
     */
    private $mailer;

    /**
     * Reply To addresses
     * @var array
     */
    private $replyToAddresses = [];

    /**
     * CC addresses
     * @var array
     */
    private $ccAddresses = [];

    /**
     * BCC addresses
     * @var array
     */
    private $bccAddresses = [];

    /**
     * Email attachments
     * @var array
     */
    private $attachments = [];

    /**
     * Email attachments created from string
     * @var array
     */
    private $stringAttachments = [];

    /**
     * @var SmtpAdapter|null
     */
    private static $instance = null;

    /**
     * SmtpAdapter constructor
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->mailer = new PHPMailer(true);

        $this->mailer->CharSet = 'UTF-8';

        $this->setupSmtp($params);

        if (config()->get('debug')) {
            $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;

            $this->mailer->Debugoutput = function ($message) {
                warning($message, ['tab' => Debugger::MAILS]);
            };
        }
    }

    /**
     * Sets "Reply-To" address
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setReplay(string $email, ?string $name = null): SmtpAdapter
    {
        $this->replyToAddresses[] = [
            'email' => $email,
            'name' => $name
        ];

        return $this;
    }

    /**
     * Gets "Reply-To" addresses
     * @return array
     */
    public function getReplays(): array
    {
        return $this->replyToAddresses;
    }

    /**
     * Sets "CC" address
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setCC(string $email, ?string $name = null): SmtpAdapter
    {
        $this->ccAddresses[] = [
            'email' => $email,
            'name' => $name
        ];

        return $this;
    }

    /**
     * Gets "CC" addresses
     * @return array
     */
    public function getCCs(): array
    {
        return $this->ccAddresses;
    }

    /**
     * Sets "BCC" address
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setBCC(string $email, ?string $name = null): SmtpAdapter
    {
        $this->bccAddresses[] = [
            'email' => $email,
            'name' => $name
        ];

        return $this;
    }

    /**
     * Get "BCC" addresses
     * @return array
     */
    public function getBCCs(): array
    {
        return $this->bccAddresses;
    }

    /**
     * Sets attachments from the path on the filesystem
     * @param string $attachment
     * @return $this
     */
    public function setAttachment(string $attachment): SmtpAdapter
    {
        $this->attachments[] = $attachment;;
        return $this;
    }

    /**
     * Gets the attachments
     * @return array
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * Sets attachment from the string
     * @param string $content
     * @param string $filename
     * @return $this
     */
    public function setStringAttachment(string $content, string $filename): SmtpAdapter
    {
        $this->stringAttachments[] = [
            'content' => $content,
            'filename' => $filename
        ];

        return $this;
    }

    /**
     * Gets the string attachments
     * @return array
     */
    public function getStringAttachments(): array
    {
        return $this->stringAttachments;
    }

    /**
     * Setups the SMTP
     * @param array $params
     */
    private function setupSmtp(array $params)
    {
        $this->mailer->isSMTP();
        $this->mailer->SMTPAuth = true;
        $this->mailer->Host = $params['host'];
        $this->mailer->SMTPSecure = $params['secure'];
        $this->mailer->Port = $params['port'];
        $this->mailer->Username = $params['username'];
        $this->mailer->Password = $params['password'];
    }

    /**
     * Prepares the data
     * @throws Exception
     */
    private function prepare()
    {
        $this->mailer->setFrom($this->from['email'], $this->from['name']);

        if ($this->subject) {
            $this->mailer->Subject = $this->subject;
        }

        if ($this->message) {
            if ($this->templatePath) {
                $body = $this->createFromTemplate();
            } else {
                $body = is_array($this->message) ? implode($this->message) : $this->message;
            }

            $this->mailer->Body = trim(str_replace("\n", "", $body));
        }

        $this->fillProperties('addAddress', $this->addresses);
        $this->fillProperties('addReplyTo', $this->replyToAddresses);
        $this->fillProperties('addCC', $this->ccAddresses);
        $this->fillProperties('addBCC', $this->bccAddresses);
        $this->fillProperties('addAttachment', $this->attachments);
        $this->fillProperties('addStringAttachment', $this->stringAttachments);
    }

    /**
     * Fills the php mailer properties
     * @param string $method
     * @param array $fields
     */
    private function fillProperties(string $method, array $fields = [])
    {
        if (!empty($fields)) {
            foreach ($fields as $field) {
                if (is_string($field)) {
                    $this->mailer->$method($field);
                } else {
                    $valOne = current($field);
                    next($field);
                    $valTwo = current($field);
                    $this->mailer->$method($valOne, $valTwo);
                    reset($field);
                }
            }
        }
    }

    /**
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function sendEmail(): bool
    {
        return $this->mailer->send();
    }
}