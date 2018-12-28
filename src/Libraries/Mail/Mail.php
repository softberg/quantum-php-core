<?php

namespace Quantum\Libraries\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use Quantum\Routes\RouteController;

class Mail
{
    private $mail;

    public function __construct()
    {
        $this->phpMailerSettings();
    }

    private function phpMailerSettings() {
        $mailer = new PHPMailer();
		if (strlen(env('MAIL_HOST')) > 0) {					      // Enable verbose debug output
			$mailer->SMTPDebug = 2;                                 // Set mailer to use SMTP
			$mailer->isSMTP();                                      // Specify main and backup SMTP servers
			$mailer->Host = env('MAIL_HOST');                       // Enable SMTP authentication
			$mailer->SMTPAuth = true;                               // Enable TLS encryption, `ssl` also accepted
			$mailer->SMTPSecure = env('MAIL_SMTP_SECURE');          
			$mailer->Port =  env('MAIL_PORT');					  // TCP port to connect to
			$mailer->Username = env('MAIL_USERNAME');               // username
			$mailer->Password = env('MAIL_PASSWORD');               // password
		} else {
			$mailer->isMail();
		}
		
		$mailer->isHTML(true);
        
        $this->mail = $mailer;
    }

    public function sand($from=[], $users_mail=[], $message, $options=[]) {
        $this->mail->setFrom($from['email'], $from['name']);
        $body = '';

		if(array_key_exists('email', $users_mail) {
			$this->mail->addAddress($users_mail['email'], $users_mail['name']);
		} else {
			foreach ($users_mail as $user) {
				$this->mail->addAddress($user['email'], $user['name']);
			}
		}

        if($options['subject']) {
            $this->mail->Subject = $options['subject'];
        }

        if($options['template']) {
            ob_start();
            ob_implicit_flush(false);

            if(is_array($message) && !empty($message)) {
                extract($message, EXTR_OVERWRITE);
            }

            $current_module = RouteController::$currentRoute['module'];
            $template =  MODULES_DIR . '/' . $current_module . '/Views/'. $options['template'] .'.php';
            require $template;
            $body = ob_get_clean();
        } else {
            if(!s_array($message)) {
                $body = $message;
            }
        }

        $this->mail->Body = $body;

        if(is_array ($options['attachments']) && count($options['attachments']) > 0) {
            foreach ($options['attachments'] as $attachment) {
                $this->mail->addAttachment($attachment);
            }
        } else if(!empty($options['attachments'])){
			$this->mail->addAttachment($options['attachments']);
		} 

        if(is_array ($options['replayto']) && count($options['replayto']) > 0) {
            foreach ($options['replayto'] as $replayto) {
                $this->mail->addReplyTo($replayto);
            }
        } else if(!empty($options['replayto'])){
			$this->mail->addReplyTo($options['replayto']);
		} 

        if(is_array ($options['cc']) && count($options['cc']) > 0) {
            foreach ($options['cc'] as $cc) {
                $this->mail->addCC($cc);
            }
        } else if(!empty($options['cc'])){
			$this->mail->addCC($options['cc']);
		} 

        if(is_array ($options['bcc']) && count($options['bcc']) > 0) {
            foreach ($options['bcc'] as $bcc) {
                $this->mail->addBCC($bcc);
            }
        } else if(!empty($options['bcc'])){
			$this->mail->addBCC($options['bcc']);
		} 

        if($this->mail->send()) {
            return true;
        } else {
            return false;
        }
    }

}