<?php

namespace Quantum\Libraries\Mailer;

use PHPMailer\PHPMailer\PHPMailer;
use Quantum\Routes\RouteController;

class Mailer
{
    private $mailer;

    public function __construct()
    {
        $this->phpMailerSettings();
    }

    private function phpMailerSettings()
    {
        $phpMailer = new PHPMailer();
        if (strlen(env('MAIL_HOST')) > 0) {
            $phpMailer->SMTPDebug = 2;
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

    public function sand($from = [], $addresses = [], $message, $options = [])
    {
        $this->mailer->setFrom($from['email'], $from['name']);

        $this->createAddresses($addresses);

        if ($options['subject']) {
            $this->mailer->Subject = $options['subject'];
        }

        $body = '';
        if ($options['template']) {
            $body = $this->createFromTemplate($message, $options['template']);
        } else {
            if (!is_array($message)) {
                $body = $message;
            }
        }

        $this->mailer->Body = $body;

        if ($options['attachments']) {
            $this->createAttachments($options['attachments']);
        }

        if ($options['replayto']) {
            $this->createReplays($options['replayto']);
        }

        if ($options['cc']) {
            $this->createCCs($options['cc']);
        }

        if ($options['bcc']) {
            $this->createBCCs($options['bcc']);
        }

        if ($this->mailer->send()) {
            return true;
        } else {
            return false;
        }
    }

    private function createFromTemplate($message, $template)
    {
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

    private function createAttachments($attachments)
    {
        if (is_array($attachments) && count($attachments) > 0) {
            foreach ($attachments as $attachment) {
                $this->mailer->addAttachment($attachment);
            }
        } else if (!empty($attachments)) {
            $this->mailer->addAttachment($attachments);
        }

    }

    private function createAddresses($addresses)
    {
        if (array_key_exists('email', $addresses)) {
            $this->mailer->addAddress($addresses['email'], $addresses['name']);
        } else {
            foreach ($addresses as $address) {
                $this->mailer->addAddress($address['email'], $address['name']);
            }
        }

    }

    private function createReplays($addresses)
    {
        if (is_array($addresses) && count($addresses) > 0) {
            foreach ($addresses as $address) {
                $this->mailer->addReplyTo($address);
            }
        } else if (!empty($addresses)) {
            $this->mailer->addReplyTo($addresses);
        }

    }

    private function createCCs($addresses)
    {
        if (is_array($addresses) && count($addresses) > 0) {
            foreach ($addresses as $address) {
                $this->mailer->addCC($address);
            }
        } else if (!empty($addresses)) {
            $this->mailer->addCC($addresses);
        }

    }

    private function createBCCs($addresses)
    {
        if (is_array($addresses) && count($addresses) > 0) {
            foreach ($addresses as $address) {
                $this->mailer->addBCC($address);
            }
        } else if (!empty($addresses)) {
            $this->mailer->addBCC($addresses);
        }

    }

}