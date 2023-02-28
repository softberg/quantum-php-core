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

class SendgridAdapter implements MailerInterface
{
    /**
     * Send mail by using Sendgrid
     * @param  string $data
     * @return bool
     */
    public function sendMail($data)
    {
        try {
            $httpClient = new HttpClient();
            $httpClient->createMultiRequest()
                ->createRequest('https://api.sendgrid.com/v3/mail/send')
                ->setMethod('POST')
                ->setHeaders([
                    'Authorization: Bearer ' . config()->get('mailer.sendgrid.api_key'),
                    'Content-Type: application/json'
                ])
                ->setData($data)
                ->start();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
