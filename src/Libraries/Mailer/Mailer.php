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
 * @since 2.0.0
 */

namespace Quantum\Libraries\Mailer;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Mailer class
 *
 * @package Quantum
 * @subpackage Libraries.Mailer
 * @category Libraries
 * @uses \PHPMailer
 */
class Mailer
{

    /**
     * PHP Mailer instance
     * @var object
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
     * Class constructor
     */
    public function __construct()
    {
        $this->mailer = new PHPMailer();

        if (strlen(env('MAIL_HOST')) > 0) {
            $this->setupSmtp();
            $this->setupDebugging();
        } else {
            $this->mailer->isMail();
        }

        $this->mailer->AllowEmpty = true;
        $this->mailer->isHTML(true);
    }

    /**
     * Creates the from email and the name
     * @param array $from
     * @return $this
     */
    public function setFrom($email, $name = null)
    {
        $this->from['email'] = $email;
        $this->from['name'] = $name;
        return $this;
    }

    /**
     * Gets from email and the name
     * @return array
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Creates "To" addresses
     * @param string $email
     * @param string $name
     * @return $this
     */
    public function setAddress(string $email, string $name = null)
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
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Creates "Reply-To" addresses
     * @param string $email
     * @param string $name
     * @return $this
     */
    public function setReplay(string $email, string $name = null)
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
    public function getReplayes()
    {
        return $this->replyToAddresses;
    }

    /**
     * Creates "CC" addresses
     * @param string $email
     * @param string $name
     * @return $this
     */
    public function setCC(string $email, string $name = null)
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
    public function getCCs()
    {
        return $this->ccAddresses;
    }

    /**
     * Creates "BCC" addresses
     * @param string $email
     * @param type $name
     * @return $this
     */
    public function setBCC(string $email, $name = null)
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
    public function getBCCs()
    {
        return $this->bccAddresses;
    }

    /**
     * Creates the subject
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Gets the subject
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Sets the template
     * @param string $templatePath
     * @return $this
     */
    public function setTemplate($templatePath)
    {
        $this->templatePath = $templatePath;
        return $this;
    }

    /**
     * Gets the template
     * @return string
     */
    public function getTemplate()
    {
        return $this->templatePath;
    }

    /**
     * Creates the body
     * @param string|array $message
     * @return $this
     */
    public function setBody($message)
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
     * Creates attachments from the path on the filesystem
     * @param mixed $attachments
     * @return $this
     */
    public function setAttachments(array $attachments)
    {
        $this->attachments = $attachments;
        return $this;
    }

    /**
     * Gets the attachments
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Creates attachments from the string
     * @param string $content
     * @param string $filename
     * @return $this
     */
    public function setStringAttachment($content, $filename)
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
    public function getStringAttachment()
    {
        return $this->stringAttachments;
    }

    /**
     * Sends the email
     * @param array|null $from
     * @param array|null $addresses
     * @param string|null $message
     * @param array $options
     * @return bool
     */
    public function send(array $from = null, array $address = null, $message = null, $options = [])
    {
        if ($from) {
            $this->setFrom(...$from);
        }

        if ($address) {
            $this->setAddress(...$address);
        }

        if (isset($options['replayto'])) {
            $this->setReplay(...$options['replayto']);
        }

        if (isset($options['cc'])) {
            $this->setCC(...$options['cc']);
        }

        if (isset($options['bcc'])) {
            $this->setBCC(...$options['bcc']);
        }

        if (isset($options['subject'])) {
            $this->setSubject($options['subject']);
        }

        if ($message) {
            $this->setBody($message);
        }

        if (isset($options['attachments'])) {
            $this->setAttachments($options['attachments']);
        }

        if (isset($options['stringAttachment'])) {
            $this->setStringAttachments($options['content'], $options['filename']);
        }

        $this->prepare();

        if ($this->mailer->send()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Prepares the data
     */
    private function prepare()
    {
        $this->mailer->setFrom($this->from['email'], $this->from['name']);

        if ($this->addresses) {
            foreach ($this->addresses as $address) {
                $this->mailer->addAddress($address['email'], $address['name']);
            }
        }

        if ($this->replyToAddresses) {
            foreach ($this->replyToAddresses as $address) {
                $this->mailer->addReplyTo($address['email'], $address['name']);
            }
        }

        if ($this->ccAddresses) {
            foreach ($this->ccAddresses as $address) {
                $this->mailer->addCC($address['email'], $address['name']);
            }
        }
        if ($this->bccAddresses) {
            foreach ($this->bccAddresses as $address) {
                $this->mailer->addBCC($address['email'], $address['name']);
            }
        }

        if ($this->subject) {
            $this->mailer->Subject = $this->subject;
        }

        if ($this->message) {
            $body = '';

            if ($this->templatePath) {
                $body = $this->createFromTemplate();
            } else {
                $body = is_array($this->message) ? implode($this->message) : $this->message;
            }

            $this->mailer->Body = $body;
        }

        if ($this->attachments) {
            foreach ($this->attachments as $attachment) {
                $this->mailer->addAttachment($attachment);
            }
        }

        if ($this->stringAttachments) {
            foreach ($this->stringAttachments as $attachment) {
                $this->mailer->addStringAttachment($attachment['content'], $attachment['filename']);
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
        $this->mailer->Host = env('MAIL_HOST');
        $this->mailer->SMTPSecure = env('MAIL_SMTP_SECURE');
        $this->mailer->Port = env('MAIL_PORT');
        $this->mailer->Username = env('MAIL_USERNAME');
        $this->mailer->Password = env('MAIL_PASSWORD');
    }

    /**
     * Setups the debugging
     */
    private function setupDebugging()
    {
        if (config()->get('debug')) {
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
    private function createFromTemplate()
    {
        ob_start();
        ob_implicit_flush(false);

        if (!empty($this->message) && is_array($this->message)) {
            extract($this->message, EXTR_OVERWRITE);
        }

        require $this->templatePath . '.php';

        return ob_get_clean();
    }

}
