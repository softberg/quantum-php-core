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
use Quantum\Libraries\Mailer\MailTrap;
use Exception;

/**
 * class SendgridAdapter
 * @package Quantum\Libraries\Mailer
 */
class SendgridAdapter implements MailerInterface
{
    use MailerAdapterTrait;

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
    private function __construct(array $params)
    {
        $this->httpClient = new HttpClient();

        $this->apiKey = $params['api_key'];
    }

    /**
     * Get Instance
     * @param array $params
     * @return SendgridAdapter|null
     */
    public static function getInstance(array $params): ?SendgridAdapter
    {
        if (self::$instance === null) {
            self::$instance = new self($params);
        }

        return self::$instance;
    }

    /**
     * Prepares the data
     */
    private function prepare()
    {
        $this->data['from'] = [
            'email' => $this->from['email']
        ];

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
                $body = is_array($this->message) ? implode($this->message) : $this->message;
            }

            $this->data['content'] = [
                'type' => 'text/html',
                'value' => trim(str_replace("\n", "", $body))
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

    /**
     * @return bool
     * @throws Exception
     */
    private function saveEmail(): bool
    {
        return MailTrap::getInstance()->saveMessage($this->getMessageId(), $this->getMessageContent());
    }
}
