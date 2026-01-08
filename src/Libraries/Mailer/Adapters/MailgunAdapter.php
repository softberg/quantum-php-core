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
 * @since 3.0.0
 */

namespace Quantum\Libraries\Mailer\Adapters;

use Quantum\Libraries\Mailer\Contracts\MailerInterface;
use Quantum\Libraries\Mailer\Traits\MailerTrait;
use Quantum\Libraries\HttpClient\HttpClient;
use Exception;

/**
 * class MailgunAdapter
 * @package Quantum\Libraries\Mailer
 */
class MailgunAdapter implements MailerInterface
{

    use MailerTrait;

    /**
     * @var string
     */
    public $name = 'Mailgun';

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiUrl = 'https://api.mailgun.net/v3/';

    /**
     * @var array
     */
    private $data = [];

    /**
     * MailgunAdapter constructor
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->httpClient = new HttpClient();

        $this->apiKey = $params['api_key'];
        $this->apiUrl .= $params['domain'] . '/messages';
    }

    /**
     * Prepares the data
     */
    private function prepare()
    {
        $this->data['from'] = $this->from['name'] . " " . $this->from['email'];

        $to = [];
        foreach ($this->addresses as $address) {
            $to[] = $address['email'];
        }

        $this->data['to'] = implode(',', $to);

        if ($this->subject) {
            $this->data['subject'] = $this->subject;
        }

        if ($this->message) {
            if ($this->templatePath) {
                $body = $this->createFromTemplate();
            } else {
                $body = is_array($this->message) ? implode('', $this->message) : $this->message;
            }

            $this->data['html'] = trim(str_replace("\n", "", $body));
        }
    }

    /**
     * @return bool
     */
    private function sendEmail(): bool
    {
        try {
            $this->httpClient
                ->createRequest($this->apiUrl)
                ->setMethod('POST')
                ->setHeaders([
                    'Authorization' => 'Basic ' . base64_encode('api:' . $this->apiKey),
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ])
                ->setData(json_encode($this->data))
                ->start();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}