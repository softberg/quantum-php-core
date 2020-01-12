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
 * @since 1.0.0
 */

namespace Quantum\Libraries\Mailer;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Mailer class
 *
 * @package Quantum
 * @subpackage Libraries.Mailer
 * @category Libraries
 */
class Mailer
{

    /**
     * PHP Mailer instance
     *
     * @var object
     */
    private $mailer;

    /**
     * Template path
     * 
     * @var string 
     */
    private $templatePath = '';

    /**
     * PHP Mailer Log
     *
     * @var string
     */
    private $log;

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->setupPHPMailer();
    }

    /**
     * PHP Mailer Settings
     *
     * Configures the PHP Mailer
     *
     * @return void
     * @uses \PHPMailer
     */
    private function setupPHPMailer()
    {
        $phpMailer = new PHPMailer();
        if (strlen(env('MAIL_HOST')) > 0) {
            $this->setupSmtp($phpMailer);
        } else {
            $phpMailer->isMail();
        }

        $phpMailer->isHTML(true);
        $this->mailer = $phpMailer;
    }

    private function setupSmtp($phpMailer)
    {
        $this->setupDebugging($phpMailer);

        $phpMailer->isSMTP();
        $phpMailer->Host = env('MAIL_HOST');
        $phpMailer->SMTPAuth = true;
        $phpMailer->SMTPSecure = env('MAIL_SMTP_SECURE');
        $phpMailer->Port = env('MAIL_PORT');
        $phpMailer->Username = env('MAIL_USERNAME');
        $phpMailer->Password = env('MAIL_PASSWORD');
    }

    private function setupDebugging()
    {
        if (get_config('debug')) {
            $phpMailer->SMTPDebug = 1;
            $phpMailer->Debugoutput = function ($str, $level) {
                $this->log .= $str . '&';
                session()->set('mail_log', $this->log);
            };
        } else {
            $phpMailer->SMTPDebug = 0;
        }
    }

    /**
     * Send
     *
     * Send the email
     *
     * @param array|null $from
     * @param array|null $addresses
     * @param string|null $message
     * @param array $options
     * @return bool
     * @uses \PHPMailer
     */
    public function send(array $from = null, array $addresses = null, $message = null, $options = [])
    {
        if ($from) {
            $this->createFrom($from);
        }

        if ($addresses) {
            $this->createAddresses($addresses);
        }

        if (isset($options['subject'])) {
            $this->createSubject($options['subject']);
        }

        if ($message) {
            $this->createBody($message, $options);
        }

        if (isset($options['attachments'])) {
            $this->createAttachments($options['attachments']);
        }

        if (isset($options['stringAttachment'])) {
            $this->createStringAttachments($options['stringAttachment']);
        }

        if (isset($options['replayto'])) {
            $this->createReplays($options['replayto']);
        }

        if (isset($options['cc'])) {
            $this->createCCs($options['cc']);
        }

        if (isset($options['bcc'])) {
            $this->createBCCs($options['bcc']);
        }

        if ($this->mailer->send()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Create From
     *
     * Sets from email and name
     *
     * @param array $from
     * @return $this
     */
    public function createFrom(array $from)
    {
        $this->mailer->setFrom($from['email'], $from['name']);
        return $this;
    }

    /**
     * Create Subject
     *
     * @param string $subject
     * @return $this
     */
    public function createSubject($subject)
    {
        $this->mailer->Subject = $subject;
        return $this;
    }

    /**
     * Set Template
     * 
     * @param string $templatePath
     * @return $this
     */
    public function setTemplate($templatePath)
    {
        $this->templatePath = $templatePath;
        return $this;
    }

    /**
     * Create Body
     *
     * @param mixed $message
     * @return $this
     */
    public function createBody($message)
    {
        $body = '';
        if ($this->templatePath) {
            $body = $this->createFromTemplate($message, $this->templatePath);
        } else {
            if (!is_array($message)) {
                $body = $message;
            }
        }

        $this->mailer->AllowEmpty = true;
        $this->mailer->Body = $body;
        return $this;
    }

    /**
     * Create Attachments
     *
     * Add attachments from a path
     *
     * @param mixed $attachments
     * @return $this
     * @uses \PHPMailer
     */
    public function createAttachments($attachments)
    {
        if (is_array($attachments) && count($attachments) > 0) {
            foreach ($attachments as $attachment) {
                $this->mailer->addAttachment($attachment);
            }
        } else if (!empty($attachments)) {
            $this->mailer->addAttachment($attachments);
        }

        return $this;
    }

    /**
     * Create String Attachments
     *
     * Add attachments from a string
     *
     * @param mixed $attachments
     * @return $this
     * @uses \PHPMailer
     */
    public function createStringAttachments($attachments)
    {
        if (array_key_exists('string', $attachments)) {
            $this->mailer->addStringAttachment($attachments['string'], $attachments['name']);
        } else {
            foreach ($attachments as $attachment) {
                $this->mailer->addStringAttachment($attachment['string'], $attachment['name']);
            }
        }
        return $this;
    }

    /**
     * Create Addresses
     *
     * Add a "To" addresses
     *
     * @param mixed $addresses
     * @return $this
     * @uses \PHPMailer
     */
    public function createAddresses($addresses)
    {
        if (array_key_exists('email', $addresses)) {
            $this->mailer->addAddress($addresses['email'], $addresses['name']);
        } else {
            foreach ($addresses as $address) {
                $this->mailer->addAddress($address['email'], $address['name']);
            }
        }

        return $this;
    }

    /**
     * Create Replays
     *
     * Add a "Reply-To" addresses
     *
     * @param mixed $addresses
     * @return $this
     * @uses \PHPMailer
     */
    public function createReplays($addresses)
    {
        if (is_array($addresses) && count($addresses) > 0) {
            foreach ($addresses as $address) {
                $this->mailer->addReplyTo($address);
            }
        } else if (!empty($addresses)) {
            $this->mailer->addReplyTo($addresses);
        }

        return $this;
    }

    /**
     * Create CCs
     *
     * Add a "CC" addresses
     *
     * @param mixed $addresses
     * @return $this
     * @uses \PHPMailer
     */
    public function createCCs($addresses)
    {
        if (is_array($addresses) && count($addresses) > 0) {
            foreach ($addresses as $address) {
                $this->mailer->addCC($address);
            }
        } else if (!empty($addresses)) {
            $this->mailer->addCC($addresses);
        }

        return $this;
    }

    /**
     * Create BCCs
     *
     * Add a "BCC" addresses
     *
     * @param mixed $addresses
     * @return $this
     * @uses \PHPMailer
     */
    public function createBCCs($addresses)
    {
        if (is_array($addresses) && count($addresses) > 0) {
            foreach ($addresses as $address) {
                $this->mailer->addBCC($address);
            }
        } else if (!empty($addresses)) {
            $this->mailer->addBCC($addresses);
        }

        return $this;
    }

    /**
     * Create From Template
     *
     * Create message body from email template
     *
     * @param mixed $message
     * @param string $template
     * @return string
     */
    private function createFromTemplate($message, $template)
    {
        ob_start();
        ob_implicit_flush(false);

        if (!empty($message) && is_array($message)) {
            extract($message, EXTR_OVERWRITE);
        }

        require $template . '.php';
        ;
        return ob_get_clean();
    }

}
