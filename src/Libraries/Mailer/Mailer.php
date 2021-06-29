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
 * @since 2.4.0
 */

namespace Quantum\Libraries\Mailer;

use PHPMailer\PHPMailer\PHPMailer;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Di\Di;

/**
 * Mailer class
 *
 * @package Quantum
 * @subpackage Libraries.Mailer
 * @category Libraries
 * @uses \PHPMailer\PHPMailer\PHPMailer
 */
class Mailer
{

    /**
     * PHP Mailer instance
     * @var \PHPMailer\PHPMailer\PHPMailer
     */
    private $mailer;

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
     * Template path
     * @var string
     */
    private $templatePath;

    /**
     * PHP Mailer Log
     * @var string
     */
    private $log;

    /**
     * Mailer constructor.
     */
    public function __construct()
    {
        $this->mailer = new PHPMailer();

        if (config()->has('MAIL_HOST')) {
            $this->setupSmtp();
            $this->setupDebugging();
        } else {
            $this->mailer->isMail();
        }

        $this->mailer->AllowEmpty = true;
        $this->mailer->isHTML(true);
    }

    /**
     * Sets the from email and the name
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setFrom(string $email, ?string $name = null): Mailer
    {
        $this->from['email'] = $email;
        $this->from['name'] = $name;
        return $this;
    }

    /**
     * Gets from email and the name
     * @return array
     */
    public function getFrom(): array
    {
        return $this->from;
    }

    /**
     * Sets "To" addresses
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setAddress(string $email, ?string $name = null): Mailer
    {
        array_push($this->addresses, [
            'email' => $email,
            'name' => $name
        ]);

        return $this;
    }

    /**
     * Gets "To" addresses
     * @return array
     */
    public function getAddresses(): array
    {
        return $this->addresses;
    }

    /**
     * Sets "Reply-To" addresses
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setReplay(string $email, ?string $name = null): Mailer
    {
        array_push($this->replyToAddresses, [
            'email' => $email,
            'name' => $name
        ]);

        return $this;
    }

    /**
     * Gets "Reply-To" addresses
     * @return array
     */
    public function getReplayes(): array
    {
        return $this->replyToAddresses;
    }

    /**
     * Sets "CC" addresses
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setCC(string $email, ?string $name = null): Mailer
    {
        array_push($this->ccAddresses, [
            'email' => $email,
            'name' => $name
        ]);

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
     * Sets "BCC" addresses
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setBCC(string $email, ?string $name = null): Mailer
    {
        array_push($this->bccAddresses, [
            'email' => $email,
            'name' => $name
        ]);

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
     * Sets the subject
     * @param string $subject
     * @return $this
     */
    public function setSubject(string $subject): Mailer
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
     * @return $this
     */
    public function setTemplate(string $templatePath): Mailer
    {
        $this->templatePath = $templatePath;
        return $this;
    }

    /**
     * Gets the template
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->templatePath;
    }

    /**
     * Sets the body
     * @param string|array $message
     * @return $this
     */
    public function setBody($message): Mailer
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Gets the body
     * @return string|array
     */
    public function getBody()
    {
        return $this->message;
    }

    /**
     * Sets attachments from the path on the filesystem
     * @param string $attachments
     * @return $this
     */
    public function setAttachment(string $attachments): Mailer
    {
        array_push($this->attachments, $attachments);
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
    public function setStringAttachment(string $content, string $filename): Mailer
    {
        array_push($this->stringAttachments, [
            'content' => $content,
            'filename' => $filename
        ]);

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
     * Sends the email
     * @param array|null $from
     * @param array|null $address
     * @param string|null $message
     * @param array|null $options
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    public function send(?array $from = null, ?array $address = null, ?string $message = null, ?array $options = []): bool
    {
        if ($from) {
            $this->setFrom(...$from);
        }

        if ($address) {
            $this->setAddress(...$address);
        }

        if ($message) {
            $this->setBody($message);
        }

        $this->setOptions($options);

        $this->prepare();

        if (config()->has('mail_trap')) {
            $sent = $this->mailer->preSend();
            $this->saveMessage($this->mailer->getLastMessageID(), $this->mailer->getSentMIMEMessage());
            return $sent;
        } else {
            return $this->mailer->send();
        }
    }

    /**
     * Save the message on local file
     * @param string $id
     * @param string $content
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    private function saveMessage(string $id, string $content)
    {
        $fs = Di::get(FileSystem::class);

        $emailsDirectory = base_dir() . DS . 'base' . DS . 'emails';

        if ($fs->isDirectory($emailsDirectory)) {
            $fs->put($emailsDirectory . DS . $this->getFilename($id), $content);
        }
    }

    /**
     * Sets the options
     * @param array $options
     */
    private function setOptions(array $options)
    {
        foreach ($options as $name => $params) {
            if (method_exists(__CLASS__, $method = 'set' . ucfirst($name))) {
                if (is_array($params)) {
                    $this->$method(...$params);
                } else {
                    $this->$method($params);
                }
            }
        }
    }

    /**
     * Fetches message ID
     * @param string $lastMessageId
     * @return string
     */
    private function getFilename(string $lastMessageId): string
    {
        preg_match('/<(.*?)@/', $lastMessageId, $matches);
        return $matches[1] . '.eml';
    }

    /**
     * Prepares the data
     * @throws \PHPMailer\PHPMailer\Exception
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

            $this->mailer->Body = $body;
        }

        $this->fillProperties('addAddress', $this->addresses);

        $this->fillProperties('addReplyTo', $this->replyToAddresses);

        $this->fillProperties('addCC', $this->ccAddresses);

        $this->fillProperties('addBCC', $this->bccAddresses);

        $this->fillProperties('addAttachment', $this->attachments);

        $this->fillProperties('addStringAttachment', $this->stringAttachments);
    }

    /**
     * Files the php mailer properties
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
     * Setups SMTP
     */
    private function setupSmtp()
    {
        $this->mailer->isSMTP();
        $this->mailer->SMTPAuth = true;
        $this->mailer->Host = config()->get('mail_host');
        $this->mailer->SMTPSecure = config()->get('mail_secure');
        $this->mailer->Port = config()->get('mail_port');
        $this->mailer->Username = config()->get('mail_username');
        $this->mailer->Password = config()->get('mail_password');
    }

    /**
     * Setups the debugging
     */
    private function setupDebugging()
    {
        if (config()->has('debug')) {
            $this->mailer->SMTPDebug = 1;
            $this->mailer->Debugoutput = function ($str, $level) {
                $this->log .= $str . '&';
                session()->set('_qt_mailer_log', $this->log);
            };
        } else {
            $this->mailer->SMTPDebug = 0;
        }
    }

    /**
     * Create message body from email template
     * @return string
     */
    private function createFromTemplate(): string
    {
        ob_start();
        ob_implicit_flush(0);

        if (!empty($this->message) && is_array($this->message)) {
            extract($this->message, EXTR_OVERWRITE);
        }

        require $this->templatePath . '.php';

        return ob_get_clean();
    }

}
