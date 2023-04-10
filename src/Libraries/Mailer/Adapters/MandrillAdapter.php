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
 * class MandrillAdapter
 * @package Quantum\Libraries\Mailer
 */
class MandrillAdapter implements MailerInterface
{
    use MailerAdapterTrait;

    /**
     * @var string
     */
    public $name = 'Mandrill';

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
    private $apiUrl = 'https://mandrillapp.com/api/1.0/messages/send.json';

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var MandrillAdapter|null
     */
    private static $instance = null;

    /**
     * MandrillAdapter constructor
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
     * @return MandrillAdapter|null
     */
    public static function getInstance(array $params): ?MandrillAdapter
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
        $message = [];

        $message['from_email'] = $this->from['email'];

        if ($this->from['name']) {
            $message['from_name'] = $this->from['name'];
        }

        $message['to'] = $this->addresses;

        if ($this->subject) {
            $message['subject'] = $this->subject;
        }

        if ($this->message) {
            if ($this->templatePath) {
                $body = $this->createFromTemplate();
            } else {
                $body = is_array($this->message) ? implode($this->message) : $this->message;
            }

            $message['html'] = trim(str_replace("\n", "", $body));
        }

        $this->data['message'] = $message;
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
