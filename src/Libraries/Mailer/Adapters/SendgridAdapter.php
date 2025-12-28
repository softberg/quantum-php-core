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
 * @since 2.9.5
 */

namespace Quantum\Libraries\Mailer\Adapters;

use Quantum\Libraries\Mailer\Contracts\MailerInterface;
use Quantum\Libraries\Mailer\Traits\MailerTrait;
use Quantum\Libraries\HttpClient\HttpClient;
use Exception;

/**
 * class SendgridAdapter
 * @package Quantum\Libraries\Mailer
 */
class SendgridAdapter implements MailerInterface
{

    use MailerTrait;

    /**
     * @var string
     */
    public $name = 'Sendgrid';

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiUrl = 'https://api.sendgrid.com/v3/mail/send';

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var SendgridAdapter|null
     */
    private static $instance = null;

    /**
     * SendgridAdapter constructor
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->httpClient = new HttpClient();

        $this->apiKey = $params['api_key'];
    }

    /**
     * Prepares the data
     */
    public function prepare()
    {
        $this->data['from'] = $this->from;

        $this->data['personalizations'] = [
            ['to' => $this->addresses]
        ];

        if ($this->subject) {
            $this->data['subject'] = $this->subject;
        }

        if ($this->message) {
            if ($this->templatePath) {
                $body = $this->createFromTemplate();
            } else {
                $body = is_array($this->message) ? implode('', $this->message) : $this->message;
            }

            $this->data['content'] = [
                [
                    'type' => 'text/html',
                    'value' => trim(str_replace("\n", "", $body))
                ]
            ];
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
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json'
                ])
                ->setData(json_encode($this->data))
                ->start();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}