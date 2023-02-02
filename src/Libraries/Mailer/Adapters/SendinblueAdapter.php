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
 * @since 2.9.0
 */

namespace Quantum\Libraries\Mailer\Adapters;

use Quantum\Libraries\Mailer\MailerInterface;
use Quantum\Libraries\Curl\HttpClient;

class SendinblueAdapter implements MailerInterface
{    
    /**
     * Send mail by using Sendinblue
     * @param  string $data
     * @return bool
     */
    public function sendMail($data)
    {
        try {
            $httpClient = new HttpClient();
            $httpClient->createMultiRequest()
                ->createRequest('https://api.sendinblue.com/v3/smtp/email')
                ->setMethod('POST')
                ->setHeaders([
                    'Accept' => 'application/json',
                    'api-key' => config()->get('mailer.sendinblue.api_key'),
                    'content-type' => 'application/json'
                ])
                ->setData($data)
                ->start();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
