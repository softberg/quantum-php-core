<?php

namespace Quantum\Libraries\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use Quantum\Routes\RouteController;

class Mail
{
    private $newMail;

    public function __construct()
    {
        $this->phpMailerSettings();
    }

    private function phpMailerSettings() {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 2;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = env('MAIL_HOST');                       // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = env('MAIL_USERNAME');               // SMTP username
        $mail->Password = env('MAIL_PASSWORD');               // SMTP password
        $mail->SMTPSecure = env('MAIL_SMTP_SECURE');          // Enable TLS encryption, `ssl` also accepted
        $mail->Port =  env('MAIL_PORT');                      // TCP port to connect to
        $this->newMail = $mail;
    }

    public function sand($from=[], $users_mail=[], $message, $options=[]) {
        $this->newMail->setFrom($from['email'], $from['name']);
        $body = '';

        foreach ($users_mail as $user) {
            $this->newMail->addAddress($user['email'], $user['name']);
        }

        if($options['subject']) {
            $this->newMail->Subject = $options['subject'];
        }

        if($options['template']) {
            $this->newMail->isHTML(true);
            ob_start();
            ob_implicit_flush(false);

            if(is_array($message) && !empty($message)) {
                extract($message, EXTR_OVERWRITE);
            }

            $current_module = RouteController::$currentRoute['module'];
            $view =  MODULES_DIR . '/' . $current_module . '/Views/'. $options['template'] .'.php';
            require $view;
            $body = ob_get_clean();
        } else {
            if(!s_array($message)) {
                $body = $message;
            }
        }

        $this->newMail->Body = $body;

        if(is_array ($options['files']) && !empty($options['files'])) {
            foreach ($options['files'] as $file) {
                $this->newMail->addAttachment($file);
            }
        }

        if(is_array ($options['replayto']) && !empty($options['replayto'])) {
            foreach ($options['replayto'] as $replayto) {
                $this->newMail->addReplyTo($replayto);
            }
        }

        if(is_array ($options['cc']) && !empty($options['cc'])) {
            foreach ($options['cc'] as $cc) {
                $this->newMail->addCC($cc);
            }
        }

        if(is_array ($options['bcc']) && !empty($options['bcc'])) {
            foreach ($options['bcc'] as $bcc) {
                $this->newMail->addBCC($bcc);
            }
        }

        if($this->newMail->send()) {
            return true;
        } else {
            return false;
        }
    }

}