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
use Quantum\Routes\RouteController;

/**
 * Mailer class
 * 
 * @package Quantum
 * @subpackage Libraries.Mailer
 * @category Libraries
 */
class Mailer {

    /**
     * PHP Mailer instance
     * 
     * @var object 
     */
    private $mailer;

    /**
     * Class constructor 
     * 
     * @return void
     */
    public function __construct() {
        $this->phpMailerSettings();
    }

    /**
     * PHP Mailer Settings
     * 
     * Configures the PHP Mailer
     * 
     * @uses \PHPMailer
     * @return void
     */
    private function phpMailerSettings() {
        $phpMailer = new PHPMailer();
        if (strlen(env('MAIL_HOST')) > 0) {
            $phpMailer->SMTPDebug = 0;
            $phpMailer->isSMTP();
            $phpMailer->Host = env('MAIL_HOST');
            $phpMailer->SMTPAuth = true;
            $phpMailer->SMTPSecure = env('MAIL_SMTP_SECURE');
            $phpMailer->Port = env('MAIL_PORT');
            $phpMailer->Username = env('MAIL_USERNAME');
            $phpMailer->Password = env('MAIL_PASSWORD');
        } else {
            $phpMailer->isMail();
        }

        $phpMailer->isHTML(true);

        $this->mailer = $phpMailer;
    }

    /**
     * Send 
     * 
     * Send the email
     * 
     * @param array $from From address
     * @param array $addresses To addresses
     * @param type $message Message content
     * @param type $options Options
     * @uses \PHPMailer
     * @return boolean
     */
    public function send(array $from, array $addresses, $message, $options = []) {
        $this->mailer->setFrom($from['email'], $from['name']);

        $this->createAddresses($addresses);

        if ($options['subject']) {
            $this->mailer->Subject = $options['subject'];
        }

        $body = '';
        if (isset($options['template'])) {
            $body = $this->createFromTemplate($message, $options['template']);
        } else {
            if (!is_array($message)) {
                $body = $message;
            }
        }

        $this->mailer->Body = $body;

        if (isset($options['attachments'])) {
            $this->createAttachments($options['attachments']);
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
     * Create From Template
     * 
     * Create message body from email template
     * 
     * @param mixed $message
     * @param string $template
     * @return string
     */
    private function createFromTemplate($message, $template) {
        ob_start();
        ob_implicit_flush(false);

        if (is_array($message) && !empty($message)) {
            extract($message, EXTR_OVERWRITE);
        }

        $current_module = RouteController::$currentRoute['module'];
        $templatePath = MODULES_DIR . '/' . $current_module . '/Views/' . $template . '.php';
        require $templatePath;
        return ob_get_clean();
    }

    /**
     * Create Attachments
     * 
     * Add attachments from a path
     * 
     * @param mixed $attachments
     * @uses \PHPMailer
     */
    private function createAttachments($attachments) {
        if (is_array($attachments) && count($attachments) > 0) {
            foreach ($attachments as $attachment) {
                $this->mailer->addAttachment($attachment);
            }
        } else if (!empty($attachments)) {
            $this->mailer->addAttachment($attachments);
        }
    }

    /**
     * Create Addresses
     * 
     * Add a "To" addresses
     * 
     * @param mixed $addresses
     * @uses \PHPMailer
     */
    private function createAddresses($addresses) {
        if (array_key_exists('email', $addresses)) {
            $this->mailer->addAddress($addresses['email'], $addresses['name']);
        } else {
            foreach ($addresses as $address) {
                $this->mailer->addAddress($address['email'], $address['name']);
            }
        }
    }

    /**
     * Create Replays
     * 
     * Add a "Reply-To" addresses
     * 
     * @param mixed $addresses
     * @uses \PHPMailer
     */
    private function createReplays($addresses) {
        if (is_array($addresses) && count($addresses) > 0) {
            foreach ($addresses as $address) {
                $this->mailer->addReplyTo($address);
            }
        } else if (!empty($addresses)) {
            $this->mailer->addReplyTo($addresses);
        }
    }

    /**
     * Create CCs
     * 
     * Add a "CC" addresses
     * 
     * @param mixed $addresses
     * @uses \PHPMailer
     */
    private function createCCs($addresses) {
        if (is_array($addresses) && count($addresses) > 0) {
            foreach ($addresses as $address) {
                $this->mailer->addCC($address);
            }
        } else if (!empty($addresses)) {
            $this->mailer->addCC($addresses);
        }
    }

    /**
     * Create BCCs
     * 
     * Add a "BCC" addresses
     * 
     * @param mixed $addresses
     * @uses \PHPMailer
     */
    private function createBCCs($addresses) {
        if (is_array($addresses) && count($addresses) > 0) {
            foreach ($addresses as $address) {
                $this->mailer->addBCC($address);
            }
        } else if (!empty($addresses)) {
            $this->mailer->addBCC($addresses);
        }
    }

}
