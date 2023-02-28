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

class MandrillAdapter implements MailerInterface
{
    /**
     * Send mail by using Mandrill
     * @param  string $data
     * @return bool
     */
    public function sendMail($data)
    {
        try {
            $data["key"] = config()->get('mailer.mandrill.api_key');

            $httpClient = new HttpClient();
            $httpClient->createMultiRequest()
                ->createRequest("https://mandrillapp.com/api/1.0/messages/send.json")
                ->setMethod("POST")
                ->setData($data)
                ->start();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
