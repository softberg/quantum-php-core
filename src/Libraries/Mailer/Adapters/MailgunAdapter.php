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
use Quantum\Exceptions\DiException;
use ReflectionException;
use Exception;

/**
 * class MailgunAdapter
 * @package Quantum\Libraries\Mailer
 */
class MailgunAdapter implements MailerInterface
{

    use MailerAdapterTrait;

    /**
     * @var string
     */
    public $name = 'Mailgun';

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
    private $apiUrl = 'https://api.mailgun.net/v3/';

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var SendgridAdapter|null
     */
    private static $instance = null;

    /**
     * MailgunAdapter constructor
     * @param array $params
     */
    private function __construct(array $params)
    {
        $this->httpClient = new HttpClient();

        $this->apiKey = $params['api_key'];
        $this->apiUrl .= $params['domain'] . '/messages';
    }

    /**
     * Get Instance
     * @param array $params
     * @return MailgunAdapter|null
     */
    public static function getInstance(array $params): ?MailgunAdapter
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
                $body = is_array($this->message) ? implode($this->message) : $this->message;
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

    /**
     * @return bool
     * @throws Exception
     */
    private function saveEmail(): bool
    {
        return MailTrap::getInstance()->saveMessage($this->getMessageId(), $this->getMessageContent());
    }
}
