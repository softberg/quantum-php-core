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

class MailgunAdapter implements MailerInterface
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
                ->createRequest('https://api.mailgun.net/v3/' . config()->get('mailer.mailgun.domain') . '/messages')
                ->setMethod('POST')
                ->setHeaders([
                    "Authorization: Basic " . base64_encode("api:" . config()->get('mailer.mailgun.api_key')),
                    "Content-Type: application/x-www-form-urlencoded"
                ])
                ->setData($data)
                ->start();
            return true;
            dd($httpClient);
        } catch (\Exception $e) {
            dd($e);
            return false;
        }
    }
}
