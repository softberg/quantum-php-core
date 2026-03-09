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

namespace Quantum\Mailer\Adapters;

use Quantum\Mailer\Contracts\MailerInterface;
use Quantum\Mailer\Traits\MailerTrait;
use PHPMailer\PHPMailer\PHPMailer;
use Quantum\Debugger\Debugger;
use PHPMailer\PHPMailer\SMTP;
use Exception;

/**
 * class SmtpAdapter
 * @package Quantum\Mailer
 */
class SmtpAdapter implements MailerInterface
{
    use MailerTrait;

    public string $name = 'SMTP';

    /**
     * SmtpAdapter constructor
     */
    public function __construct(array $params)
    {
        $this->mailer = new PHPMailer(true);

        $this->mailer->CharSet = 'UTF-8';

        $this->setupSmtp($params);

        if (config()->get('debug')) {
            $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;

            $this->mailer->Debugoutput = function ($message): void {
                warning($message, ['tab' => Debugger::MAILS]);
            };
        }
    }

    /**
     * Sets "Reply-To" address
     */
    public function setReplay(string $email, ?string $name = null): SmtpAdapter
    {
        $this->replyToAddresses[] = [
            'email' => $email,
            'name' => $name,
        ];

        return $this;
    }

    /**
     * Gets "Reply-To" addresses
     */
    public function getReplays(): array
    {
        return $this->replyToAddresses;
    }

    /**
     * Sets "CC" address
     */
    public function setCC(string $email, ?string $name = null): SmtpAdapter
    {
        $this->ccAddresses[] = [
            'email' => $email,
            'name' => $name,
        ];

        return $this;
    }

    /**
     * Gets "CC" addresses
     */
    public function getCCs(): array
    {
        return $this->ccAddresses;
    }

    /**
     * Sets "BCC" address
     */
    public function setBCC(string $email, ?string $name = null): SmtpAdapter
    {
        $this->bccAddresses[] = [
            'email' => $email,
            'name' => $name,
        ];

        return $this;
    }

    /**
     * Get "BCC" addresses
     */
    public function getBCCs(): array
    {
        return $this->bccAddresses;
    }

    /**
     * Sets attachments from the path on the filesystem
     */
    public function setAttachment(string $attachment): SmtpAdapter
    {
        $this->attachments[] = $attachment;
        ;
        return $this;
    }

    /**
     * Gets the attachments
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * Sets attachment from the string
     */
    public function setStringAttachment(string $content, string $filename): SmtpAdapter
    {
        $this->stringAttachments[] = [
            'content' => $content,
            'filename' => $filename,
        ];

        return $this;
    }

    /**
     * Gets the string attachments
     */
    public function getStringAttachments(): array
    {
        return $this->stringAttachments;
    }

    /**
     * Setups the SMTP
     */
    private function setupSmtp(array $params): void
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
    private function prepare(): void
    {
        $this->mailer->setFrom($this->from['email'], $this->from['name']);

        if ($this->subject) {
            $this->mailer->Subject = $this->subject;
        }

        if ($this->message) {
            if ($this->templatePath) {
                $body = $this->createFromTemplate();
            } else {
                $body = is_array($this->message) ? implode('', $this->message) : $this->message;
            }

            $this->mailer->Body = trim(str_replace("\n", '', $body));
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
     */
    private function fillProperties(string $method, array $fields = []): void
    {
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

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function sendEmail(): bool
    {
        return $this->mailer->send();
    }
}
